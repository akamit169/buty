<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (04/04/2018) 
 */

namespace App\Http\Controllers\Service;

use App\Http\Controllers\BaseController;
use App\Providers\UserServiceProvider;
use App\Providers\BeauticianServiceProvider;
use App\Providers\CustomerServiceProvider;
use App\Http\Requests\Service\BeauticianRegistrationRequest;
use App\Http\Requests\Service\SetupBusinessProfileRequest;
use App\Http\Requests\Service\UpdateBusinessDescriptionRequest;
use App\Http\Requests\Service\BeauticianPortfolioRequest;
use App\Http\Requests\Service\DeleteBeauticianPortfolioRequest;
use App\Http\Requests\Service\SaveBeauticianKitRequest;
use App\Http\Requests\Service\SaveExpertiseRequest;
use App\Http\Requests\Service\CreateBeauticianServiceRequest;
use App\Http\Requests\Service\DeleteBeauticianServiceRequest;
use App\Http\Requests\Service\UpdateBeauticianServiceRequest;
use App\Http\Requests\Service\UpdateServiceDescriptionTipsRequest;
use App\Http\Requests\Service\BeauticianFixhibitionRequest;
use App\Http\Requests\Service\DeleteBeauticianFixhibition;
use App\Http\Requests\Service\SetPaymentDetailsRequest;
use App\Http\Requests\Service\GetCustomerDetailsRequest;
use App\Http\Requests\Service\BeauticianCurrentBookingRequest;
use App\Http\Requests\Service\PriceRangeRequest;


class BeauticianController extends BaseController {

    /**
     * function is used to register beautician and on successful register, login user
     * @param BeauticianRegistrationRequest $request
     * @return type
     */
    public function postUserRegistration(BeauticianRegistrationRequest $request) {

        $input = $request->all();
        $certFileName = UserServiceProvider::uploadFileToS3($request->file('certificate'));
        $input['certificateFileName'] = $certFileName;
        $response = UserServiceProvider::registerBeautician($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to send new beautician registration email to beautician as well as to admin
     */
    public function postSendRegisteredEmail() {
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, TRUE);
        UserServiceProvider::sendRegisteredEmail($data);
    }

    /**
     * function is used to set up business profile
     * @param SetupBusinessProfileRequest $request
     * @return type
     */
    public function setupBusinessProfile(SetupBusinessProfileRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::setupBusinessProfile($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to update business description
     * @param UpdateBusinessDescriptionRequest $request
     * @return type
     */
    public function updateBusinessDescription(UpdateBusinessDescriptionRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::updateBusinessDescription($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to save beautician portfolio
     * @param BeauticianPortfolioRequest $request
     * @return type
     */
    public function postBeauticianPortfolio(BeauticianPortfolioRequest $request) {
        $response = BeauticianServiceProvider::saveBeauticianPortfolio($request);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to delete beautician portfolio image
     * @param DeleteBeauticianPortfolioRequest $request
     * @return type
     */
    public function deleteBeauticianPortfolio(DeleteBeauticianPortfolioRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::deleteBeauticianPortfolio($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to get list of beautician portfolio list 
     * @param BeauticianPortfolioListRequest $request
     * @return type
     */
    public function getBeauticianPortfolioList() {
        $response = BeauticianServiceProvider::getBeauticianPortfolioList();
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to save beautician kits
     * @param SaveBeauticianKitRequest $request
     * @return type
     */
    public function postSaveBeauticianKit(SaveBeauticianKitRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::saveDeleteBeauticianKit($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to get beautician kit
     * @return type
     */
    public function getBeauticianKit() {
        $userObj = \Auth::user();
        $response = BeauticianServiceProvider::getBeauticianKitList($userObj->id);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to get beautician expertise (qualifications, specialities)
     * @return type
     */
    public function getExpertise() {
        $userObj = \Auth::user();
        $response = BeauticianServiceProvider::getExpertise($userObj->id);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to save beautician expertise (qualifications, specialities)
     * @param SaveExpertiseRequest $request
     * @return type
     */
    public function saveExpertise(SaveExpertiseRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::saveExpertise($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to create service for beautician
     * @param CreateBeauticianServiceRequest $request
     * @return type
     */
    public function postCreateService(CreateBeauticianServiceRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::saveBeauticianService($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to delete service for beautician
     * @param DeleteBeauticianServiceRequest $request
     * @return type
     */
    public function deleteService(DeleteBeauticianServiceRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::deleteBeauticianService($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to get the list of services created by beautician
     * @return type
     */
    public function getService() {
        $userObj = \Auth::user();
        $response = BeauticianServiceProvider::getBeauticianService($userObj->id);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to update service for beautician
     * @param UpdateBeauticianServiceRequest $request
     * @return type
     */
    public function postUpdateService(UpdateBeauticianServiceRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::updateBeauticianService($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to update service for beautician
     * @param UpdateServiceDescriptionTipsRequest $request
     * @return type
     */
    public function updateServiceDescriptionTips(UpdateServiceDescriptionTipsRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::updateServiceDescriptionTips($input);
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to get the list of Fixhibitions created by beautician
     * @return type
     */
    public function getAllFixhibition() {

        $response = BeauticianServiceProvider::getBeauticianFixhibitionList();
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to get the list of My Fixhibition created by beautician
     * @return type
     */
    public function getMyFixhibition() {
       
        $myFixhibition = true;
        $response = BeauticianServiceProvider::getBeauticianFixhibitionList($myFixhibition);
        return $this->sendJsonResponse($response);
    }
  
    /**
     * function is used to save beautician Fixhibition
     * @param BeauticianPortfolioRequest $request
     * @return type
     */
    public function postBeauticianFixhibition(BeauticianFixhibitionRequest $request) {
        $response = BeauticianServiceProvider::saveBeauticianFixhibition($request);
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to delete Fixhibition for beautician
     * @param DeleteBeauticianServiceRequest $request
     * @return type
     */
    public function deleteFixhibition(DeleteBeauticianFixhibition $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::deleteBeauticianFixhibition($input);
        return $this->sendJsonResponse($response);
    }


    /**
     * function is used to link account details and card 
     * @param SetPaymentDetailsRequest $request
     * @return type
     */
    public function setPaymentDetails(SetPaymentDetailsRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::setPaymentDetails($input);
        return $this->sendJsonResponse($response);
    }


    /**
     * function is used to get details of a particular customer
     * @param GetCustomerDetailsRequest $request
     * @return type
     */
    public function getCustomerDetails(GetCustomerDetailsRequest $request) {
        $customerId = $request->input('customerId');
        $response = CustomerServiceProvider::getCustomerDetails($customerId);
        return $this->sendJsonResponse($response);
    }


    /**
     * function is used to get beautician current booking by date
     * @param BeauticianCurrentBookingRequest $request
     * @return type
     */
    public function getBeauticianCurrentBooking(BeauticianCurrentBookingRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::getBeauticianCurrentBooking($input);
        return $this->sendJsonResponse($response);
    }


    
    /**
     * function is used to get beautician price range
     * @param PriceRangeRequest $request
     * @return type
     */
    public function getPriceRange(PriceRangeRequest $request) {
        $beauticianId = $request->input('beauticianId');
        $response = BeauticianServiceProvider::getBeauticianPriceRange($beauticianId);
        return $this->sendJsonResponse($response);
    }

      /*
     * get privacy policy view
     */

    public function getPrivacyPolicy() {
        return view('beautician.privacy-policy')->with('api',true);
    }

    /*
     * get terms and conditions view
     */

    public function getTermsAndConditions() {
        return view('beautician.terms-and-conditions')->with('api',true);
    }

}
