<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: UserController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (06/04/2018) 
 */

namespace App\Http\Controllers\Service;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\Service\UserProfilePicRequest;
use App\Http\Requests\Service\FlagUserRequest;
use App\Providers\UserServiceProvider;
use App\Providers\BookingServiceProvider;
use App\Http\Requests\Service\UserPreviousRatingRequest;
use App\Http\Requests\Service\RaiseDisputeRequest;

use Illuminate\Http\Request;

class UserController extends BaseController {

     /**
     * function is used to forgot password email
     * @param ForgotPasswordRequest $request
     * @return type
     */
    public function postForgotPassword(ForgotPasswordRequest $request) {
        $input = $request->all();
        $response = UserServiceProvider::forgotPassword($input);
        return $this->sendJsonResponse($response);
    }

      /**
     * function is used to change user password
     * @param ForgotPasswordRequest $request
     * @return type
     */
    public function postChangePassword(ChangePasswordRequest $request) {
        $input = $request->all();
        $response = UserServiceProvider::changePassword($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to upload user profile picture
     * @param Request $request
     * @return type
     */
    public function postUserProfilePic(UserProfilePicRequest $request) {
        $response = UserServiceProvider::postUserProfilePic($request->file('profilePic'));
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to flag a user
     * @param FlagUserRequest $request
     * @return type
     */
    public function postFlagUser(FlagUserRequest $request) {
        $input = $request->all();
        $response = UserServiceProvider::flagUser($input);
        return $this->sendJsonResponse($response);
    }

     /**
     * function is used to get available flag reasons
     * @return type
     */
    public function getFlagReasons() {
        $response = UserServiceProvider::getFlagReasons(\Auth::user()->user_type);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to fetch rating reason in case rating is given below pre defined min rating
     * @return type
     */
    public function getRatingReason() {
        $response = UserServiceProvider::fetchRatingReason();
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to get the previous ratings of a user
     * @return type
     */
    public function getUserPreviousRating(UserPreviousRatingRequest $request) {
        $input = $request->all();
        $response = UserServiceProvider::fetchUserPreviousRating($input);
        return $this->sendJsonResponse($response);
    }


     /**
     * function is used to raise a dispute against a booking
     * @return type
     */
    public function raiseDispute(RaiseDisputeRequest $request) {
        $input = $request->all();
        $response = BookingServiceProvider::raiseDispute($input);
        return $this->sendJsonResponse($response);
    }


     public function testCharge(Request $request) {

        $input = $request->all();
        $stripe = new \Stripe\Stripe();
        $stripe->setApiKey(env('STRIPE_SECRET')); // secret key provided by stripe

        $charge = \App\Providers\StripeServiceProvider::createCharge('cus_Bh3VyjDBUHVZAu', 2.5, 'acct_1BJfPzHxsPDitWpA' ,1);

        dd($charge);

        // $file = \Stripe\FileUpload::retrieve("file_1Ap9QZF9mj4bDpkiNxlUmcpy");
        // dd($file);

        dd(\Stripe\Account::retrieve('acct_1AokCIF9mj4bDpki'));
        // $response = \App\Providers\StripeServiceProvider::attachIdentityVerificationDocument($input['stripeDoc']->getPathName(),'acct_1AokCIF9mj4bDpki');
        // dd($response);
    }   

    public function getStates(){
        $response = UserServiceProvider::getStates();
        return $this->sendJsonResponse($response);
    }


    public function getUserDetails(){
        $response = UserServiceProvider::getUserDetails();
        return $this->sendJsonResponse($response);
    }

    public function testUpdateSubcats() {
       UserServiceProvider::updateHairSubCat();
       UserServiceProvider::updateAestheticsSubCat();
       UserServiceProvider::updateBarberingSubCat();
       UserServiceProvider::updateBrowsSubCat();
       UserServiceProvider::updateCosmeticTatooingSubCat();
       UserServiceProvider::updateHairMakeupSubCat();
       UserServiceProvider::updateHairRemovalSubCat();
       UserServiceProvider::updateLashesSubCat();
       UserServiceProvider::updateMakeUpSubCat();
       UserServiceProvider::updateNailsSubCat();
       UserServiceProvider::updatesprayTanningSubCat();
       echo "done";
    }
    
}
