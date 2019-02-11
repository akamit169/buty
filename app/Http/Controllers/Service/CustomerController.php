<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: CustomerController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (04/04/2018) 
 */

namespace App\Http\Controllers\Service;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Service\CustomerRegistrationRequest;
use App\Http\Requests\Service\SearchBeauticianRequest;
use App\Http\Requests\Service\GetBeauticianExpertiseRequest;
use App\Http\Requests\Service\GetBeauticianKitRequest;
use App\Http\Requests\Service\GetBeauticianServicesRequest;
use App\Http\Requests\Service\GetBeauticianDetailsRequest;
use App\Http\Requests\Service\SetupCustomerProfileRequest;
use App\Http\Requests\Service\BeauticianBookingAvailabilityRequest;
use App\Http\Requests\Service\MarkBeauticianFavouriteRequest;
use App\Providers\UserServiceProvider;
use App\Providers\CustomerServiceProvider;
use App\Providers\BeauticianServiceProvider;
use App\Providers\StripeServiceProvider;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\Service\RegisterUserOnStripeRequest;
use App\Http\Requests\Service\SignupReferralRequest;
use Illuminate\Http\Request;
use App\Http\Requests\Service\CustomerCurrentBookingRequest;
use App\Http\Requests\Service\DeleteCustomerCurrentBookingRequest;

class CustomerController extends BaseController {

    /**
     * function is used to register customer with or without facebook id and on successful register, login user
     * @param CustomerRegistrationRequest $request
     * @return type
     */
    public function postUserRegistration(CustomerRegistrationRequest $request) {

        $input = $request->all();
        $response = UserServiceProvider::registerCustomer($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to login user i.e. either customers or beauticians
     * @param LoginUserRequest $request
     * @return type
     */
    public function postUserLogin(LoginUserRequest $request) {
        $input = $request->all();
        $response = UserServiceProvider::loginAppUser($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to save referral code after social signup
     * @param signupReferralRequest $request
     * @return type
     */
    public function signupReferral(SignupReferralRequest $request) {
        $input = $request->all();
        $response = UserServiceProvider::saveReferralCode($input);
        return $this->sendJsonResponse($response);
    }

    public function postUserLogout() {
        $response = UserServiceProvider::logoutAppUser();
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to set up customer profile
     * @param SetupCustomerProfileRequest $request
     * @return type
     */
    public function setupProfile(SetupCustomerProfileRequest $request) {
        $input = $request->all();
        $response = UserServiceProvider::setupCustomerProfile($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to data related to customer appearance
     * @return type
     */
    public function getAppearanceData() {
        $response = CustomerServiceProvider::getAppearanceData();
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to create customer on stripe
     * @return type
     */
    public function registerUserOnStripe(RegisterUserOnStripeRequest $request) {
        $stripeToken = $request->input('stripeToken');
        
        $user = \Auth::user();
        $user->bank_acc_no = $request->input('bankAccNo');
        $user->bank_bsb_no = $request->input('bankBsbNo');
        $user->bank_username = $request->input('bankUsername');
        $user->save();

        $response = StripeServiceProvider::registerUserOnStripe($user->email, $stripeToken);
        if ($response['success'] == true) {
            $user->stripe_customer_id = $response['customerId'];
            $user->save();
        }

        return $this->sendJsonResponse($response);
    }

    /**
     * function used to search for beautician
     * @param SearchBeauticianRequest $request
     * @return type
     */
    public function searchBeautician(SearchBeauticianRequest $request) {
        $input = $request->all();
        $response = CustomerServiceProvider::searchBeautician($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function used to get beautician expertise
     * @param GetBeauticianExpertiseRequest $request
     * @return type
     */
    public function getBeauticianExpertise(GetBeauticianExpertiseRequest $request) {
        $beauticianId = $request->input('beauticianId');
        $response = BeauticianServiceProvider::getExpertise($beauticianId);
        return $this->sendJsonResponse($response);
    }

    /**
     * function used to get beautician kit
     * @param GetBeauticianKitRequest $request
     * @return type
     */
    public function getBeauticianKit(GetBeauticianKitRequest $request) {
        $beauticianId = $request->input('beauticianId');
        $response = BeauticianServiceProvider::getBeauticianKitList($beauticianId);
        return $this->sendJsonResponse($response);
    }

    /**
     * function used to get beautician services data
     * @param GetBeauticianServicesRequest $request
     * @return type
     */
    public function getBeauticianServices(GetBeauticianServicesRequest $request) {
        $beauticianId = $request->input('beauticianId');
        $response = BeauticianServiceProvider::getBeauticianService($beauticianId);
        return $this->sendJsonResponse($response);
    }

    /**
     * function used to get beautician about us data and portfolio info
     * @param GetBeauticianDetailsRequest $request
     * @return type
     */
    public function getBeauticianDetails(GetBeauticianDetailsRequest $request) {
        $beauticianId = $request->input('beauticianId');
        $customerId = \Auth::user()->id;
        $response = BeauticianServiceProvider::getBeauticianDetails($beauticianId,$customerId);
        return $this->sendJsonResponse($response);
    }

    /**
     * function used to get a fixhibition of the beauticians
     * @return type
     */
    public function getBeauticianFixhibition() {
        $response = BeauticianServiceProvider::getBeauticianFixhibitionList();
        return $this->sendJsonResponse($response);
    }

    /**
     * function used to get beautician availability along with booked slots
     * @param BeauticianBookingAvailabilityRequest $request
     * @return type
     */
    public function getBeauticianBookingAvailability(BeauticianBookingAvailabilityRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::getBeauticianBookingAvailability($input);
        return $this->sendJsonResponse($response);
    }


     /**
     * function used to mark beautician as favourite
     * @param MarkBeauticianFavouriteRequest $request
     * @return type
     */
    public function markBeauticianFavourite(MarkBeauticianFavouriteRequest $request) {
        $beauticianId = $request->input('beauticianId');
        $response = CustomerServiceProvider::markBeauticianFavourite($beauticianId);
        return $this->sendJsonResponse($response);
    }

    /**
     * function used to get Favourite Beauticians
     * @return type
     */
    public function getFavouriteBeauticians(Request $request) {
        $page = $request->input('page',1);
        $response = CustomerServiceProvider::getFavouriteBeauticians($page);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to get customer current booking by date
     * @param CustomerCurrentBookingRequest $request
     * @return type
     */
    public function getCustomerCurrentBooking(CustomerCurrentBookingRequest $request) {
        $input = $request->all();
        $response = CustomerServiceProvider::getCustomerCurrentBooking($input);
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to deleted customer current booking
     * @param DeleteCustomerCurrentBookingRequest $request
     * @return type
     */
    public function deleteCustomerCurrentBooking(DeleteCustomerCurrentBookingRequest $request) {
        $input = $request->all();
        $response = CustomerServiceProvider::deleteCustomerCurrentBooking($input);
        return $this->sendJsonResponse($response);
    }

     /*
     * get privacy policy view
     */

    public function getPrivacyPolicy() {
        return view('customer.privacy-policy')->with('api',true);
    }

    /*
     * get terms and conditions view
     */

    public function getTermsAndConditions() {
        return view('customer.terms-and-conditions')->with('api',true);
    }
}
