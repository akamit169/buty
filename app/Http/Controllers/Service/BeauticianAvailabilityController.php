<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianAvailabilityController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (02/05/2018) 
 */

namespace App\Http\Controllers\Service;

use App\Http\Controllers\BaseController;
use App\Providers\BeauticianServiceProvider;
use App\Http\Requests\Service\BeauticianSetAvailabilityRequest;
use App\Http\Requests\Service\BeauticianGetAvailabilityRequest;

class BeauticianAvailabilityController extends BaseController {
    
    /**
     * function is used to register beautician and on successful register, login user
     * @param BeauticianRegistrationRequest $request
     * @return type
     */
    public function postSetAvailability(BeauticianSetAvailabilityRequest $request) {
        $input = $request->all(); 
        $response = BeauticianServiceProvider::setAvailability($input['arrAvailabilityDetails']);
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to update the availablity of beautician
     * @param BeauticianRegistrationRequest $request
     * @return type
     */
    public function postUpdateAvailability(BeauticianSetAvailabilityRequest $request) {
        $input = $request->all(); 
        $response = BeauticianServiceProvider::setAvailability($input['arrAvailabilityDetails']);
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to register beautician and on successful register, login user
     * @param BeauticianRegistrationRequest $request
     * @return type
     */
    public function getAvailability(BeauticianGetAvailabilityRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::getAvailability($input['date']);
        return $this->sendJsonResponse($response);
    }
}
