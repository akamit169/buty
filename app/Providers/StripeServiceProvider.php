<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: StripeServiceProvider.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (21/04/2018) 
 */

namespace App\Providers;

use App\Models\CustomerBooking;

/**
 * StripeServiceProvider class contains methods for dealing with Stripe APIs
 */
class StripeServiceProvider extends BaseServiceProvider {

    /**
     * function to register user on stripe
     *
     * @param type $email,$stripeToken
     * @return stripe customer id in response object
     */
    public static function registerUserOnStripe($email, $stripeToken) {
        try {
            $stripe = new \Stripe\Stripe();
            $stripe->setApiKey(env('STRIPE_SECRET')); // secret key provided by stripe
            $customer = \Stripe\Customer::create(array(
                        'email' => $email, // customer email id
                        'source' => $stripeToken // stripe token generated by stripe.js
            ));
            if (!empty($customer) && !empty($customer["id"])) {
                static::$data['success'] = true;
                static::$data['customerId'] = $customer->id;
            } else {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.stripe.user_registration_error');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e, $e->getMessage());
        }

        return static::$data;
    }

    /**
     * function to create direct charge for customer using stripe connect to be used for adaptive payments (stripe fee will be borne by the beautician)
     */
    public static function createCharge($stripeCustomerId, $amount, $stripeAccountId,$adminCommisionPercent) {

        try {
            $stripe = new \Stripe\Stripe();
            $stripe->setApiKey(env('STRIPE_SECRET')); // secret key provided by stripe


            $token = \Stripe\Token::create(array(
                        "customer" => $stripeCustomerId,
                            ), array("stripe_account" => $stripeAccountId));


            $stripeChargeArr = array(
                        'source' => $token->id,
                        'amount' => round($amount * 100), //cents
                        'currency' => 'AUD',
                        'capture' => false,
                        'description' => 'captured the charge for later retrieval');

            if($adminCommisionPercent != 0)
            {
                $applicationFee = ($adminCommisionPercent * $amount) / 100; // x % of total amount
                $stripeChargeArr['application_fee'] = round($applicationFee * 100);
            }

            $charge = \Stripe\Charge::create($stripeChargeArr, array("stripe_account" => $stripeAccountId)
            );


            return $charge;
        } catch (\Exception $e) {
            static::logStripeExceptionMessage($e);
            return false;
        }
    }   

    /**
     * function to create direct charge for customer using stripe connect to be used for adaptive payments (stripe fee will be borne by the beautician)
     */
    public static function createImmediateCaptureCharge($stripeCustomerId, $amount, $stripeAccountId,$adminCommisionPercent) {

        try {
            $stripe = new \Stripe\Stripe();
            $stripe->setApiKey(env('STRIPE_SECRET')); // secret key provided by stripe


            $token = \Stripe\Token::create(array(
                        "customer" => $stripeCustomerId,
                            ), array("stripe_account" => $stripeAccountId));



            $stripeChargeArr = array(
                        'source' => $token->id,
                        'amount' => round($amount * 100), //cents
                        'currency' => 'AUD',
                        'description' => 'charge captured immediately');

            if($adminCommisionPercent != 0)
            {
                $applicationFee = ($adminCommisionPercent * $amount) / 100; // x % of total amount
                $stripeChargeArr['application_fee'] = $applicationFee * 100;
            }

            \Log::info("dsds: ".json_encode($stripeChargeArr));

            $charge = \Stripe\Charge::create($stripeChargeArr, array("stripe_account" => $stripeAccountId)
            );



            return $charge;
        } catch (\Exception $e) {
            static::logStripeExceptionMessage($e);
            return false;
        }
    }

    /**
     * function to charge the beautician card and transfer payment to the platform (Admin) 
     */
    public static function createChargeForPlatform($stripeCustomerId, $amount) {

        try {
            $stripe = new \Stripe\Stripe();
            $stripe->setApiKey(env('STRIPE_SECRET')); // secret key provided by stripe


            $charge = \Stripe\Charge::create(array(
                        'customer' => $stripeCustomerId,
                        'amount' => round($amount * 100), //cents
                        'currency' => 'AUD',
                        'description' => 'charge captured immediately, cancellation payment transferred to admin account')
            );

            return $charge;
        } catch (\Exception $e) {
            static::logStripeExceptionMessage($e);
            return false;
        }
    }

    /**
     * function to capture an existing charge
     */
    public static function captureCharge($chargeId, $stripeAccountId) {

        try {
            $stripe = new \Stripe\Stripe();
            $stripe->setApiKey(env('STRIPE_SECRET')); // secret key provided by stripe


            $ch = \Stripe\Charge::retrieve($chargeId, array('stripe_account' => $stripeAccountId));
            $charge = $ch->capture();

            return $charge;
        } catch (\Exception $e) {
            static::logStripeExceptionMessage($e);
            return false;
        }
    }

    /**
     * function to upload and attach identity verification document to the managed account
     */
    public static function attachIdentityVerificationDocument($pathToFile,$stripeAccountId) {

        try {
            $stripe = new \Stripe\Stripe();
            $stripe->setApiKey(env('STRIPE_SECRET')); // secret key provided by stripe

           $response = \Stripe\FileUpload::create(
                          array(
                            "purpose" => "identity_document",
                            "file" => fopen($pathToFile, 'r')
                          ),
                          array("stripe_account" => $stripeAccountId)
                        );

           $account = \Stripe\Account::retrieve($stripeAccountId);
           $account->legal_entity->verification->document = $response->id;
           $account->save();

          return $account;

        } catch (\Exception $e) {
            static::logStripeExceptionMessage($e);
            return false;
        }
    }

}
