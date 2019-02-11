<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: PaymentServiceProvider.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (21/04/2018) 
 */

namespace App\Providers;

use App\Providers\BaseServiceProvider;
use App\Models\User;

/**
 * PaymentServiceProvider class contains methods for saving Payment details
 */
class PaymentServiceProvider extends BaseServiceProvider {

    /**
     * function is used to save payment detail
     * @param array $arrData
     * @return type
     */
    public static function savePaymentDetail($arrData) {
        static::$data['success'] = false;
        try {
            dd($arrData);
            $userObj = \Auth::user();
            $cardName = trim($arrData['cardName']);
            $cardNumber = str_replace(" ", "", $arrData['cardNumber']);
            $expiry = str_replace(" ", "", $arrData['expiry']);
            $expiry = explode('/', $expiry);

            $expiryMonth = $expiry[0];
            $expiryYear = $expiry[1];
            $cvv = trim($arrData['cvv']);
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $newCardDetail = '';
            if (is_numeric($cardNumber)) {
                $newCardDetail = \Stripe\Token::create(array("card" => array(
                                "name" => $cardName,
                                "number" => $cardNumber,
                                "exp_month" => $expiryMonth,
                                "exp_year" => $expiryYear,
                                "cvc" => $cvv,
                                "currency" => "aud"
                )));
            }
            if (!empty($userObj->stripe_customer_id)) {

                //end of fetch all the card attached to customer
                $customer = \Stripe\Customer::retrieve($userObj->stripe_customer_id);
                if (!empty($newCardDetail)) {
                    $customer->sources->create(array("source" => $newCardDetail->id));
                }
            } else { //create customer and link credit card detail
                if (!empty($newCardDetail)) {
                    $customer = \Stripe\Customer::create(array(
                                "email" => $userObj->email,
                                "source" => $newCardDetail->id));
                } else {
                    $customer = \Stripe\Customer::create(array(
                                "email" => $userObj->email));
                }
                User::where('id', $userObj->id)->update(['stripe_customer_id' => $customer->id]);
            }
            if (!empty($newCardDetail)) {
                $customer->default_source = $newCardDetail->card->id;
                $customer->save();
            }

            static::$data['success'] = true;
            static::$data['message'] = trans('messages.beautician.payment_detail_updated_success');
        } catch (\Stripe\Error\Card $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\Api $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\ApiConnection $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\Authentication $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\Base $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\InvalidRequest $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\Permission $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\RateLimit $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    public static function fetchDefaultCardDetail($stripeCustomerId) {
        $cardDetail = [];
        try {
            if (!empty($stripeCustomerId)) {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $customer = \Stripe\Customer::retrieve($stripeCustomerId);
                $cardId = $customer->default_source;
                $cardDetail = $customer->sources->retrieve($cardId);
            }
        } catch (\Stripe\Error\Card $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\Api $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\ApiConnection $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\Authentication $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\Base $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\InvalidRequest $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\Permission $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\RateLimit $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return $cardDetail;
    }

    /**
     * function is used to save bank detail
     * @param array $data
     * @return type
     */
    public static function saveBankDetail($data) {
        try {
            $dob = $data['dob'];

            $stripeAccountArr = array(
                        "managed" => true,
                        "country" => "AU",
                        "external_account" => array(
                            "object" => "bank_account",
                            "country" => "AU",
                            "currency" => "AUD",
                            "routing_number" => $data['bsb'],
                            "account_number" => $data['accountNo'],
                        ),
                        "legal_entity" => array(
                            "type" => "individual",
                            "first_name" => $data['bankFirstName'],
                            "last_name" => $data['bankLastName'],
                        ),
                        "tos_acceptance" => array(
                            "date" => strtotime(date('Y-m-d H:i:s')),
                            "ip" => $_SERVER['SERVER_ADDR']
                        )
            );


            if(!empty($dob))
            {
                $dobArr = explode("-",$dob);
                $day = $dobArr[0];
                $month = $dobArr[1];
                $year = $dobArr[2];

                $stripeAccountArr["legal_entity"]["dob"] = array(
                                "day" => $day,
                                "month" => $month,
                                "year" => $year
                            );
            }

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $acct = \Stripe\Account::create($stripeAccountArr);
            return $acct->id;
        } catch (\Stripe\Error\Card $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\Api $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\ApiConnection $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\Authentication $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\Base $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\InvalidRequest $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\Permission $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Stripe\Error\RateLimit $e) {
            static::setExceptionError($e);
            static::$data['message'] = $e->getMessage();
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to fetch beautician bank detail
     * @return type
     */
    public static function fetchDefaultBankDetail() {
        $userObj = \Auth::user();
        $bankDetail = '';
        if ($userObj->stripe_bank_account_id != '') {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $bankDetail = \Stripe\Account::retrieve($userObj->stripe_bank_account_id);
        }
        return $bankDetail;
    }

}
