<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: ServiceController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (13/04/2018) 
 */

namespace App\Http\Controllers\Service;

use App\Http\Controllers\BaseController;
use App\Providers\BeauticianServiceProvider;
use Illuminate\Http\Request;


class ServiceController extends BaseController {


    /**
     * get services along with their sub categories
     * @return type
     */
    public function getServicesList() {
        $beauticianId = \Auth::user()->id;
        $response = BeauticianServiceProvider::getServiceList($beauticianId);
        return $this->sendJsonResponse($response);
    }

    /**
     * get top level services
     * @return type
     */
    public function getTopLevelServices() {

        $response = BeauticianServiceProvider::getTopLevelServices();
        return $this->sendJsonResponse($response);
    }


     /**
     * get sub services of of the given parent services
     * @return type
     */
    public function getSubServices(Request $request) {
        $parentServiceIdArr = $request->input('parentServiceIdArr');
        $response = BeauticianServiceProvider::getSubServices($parentServiceIdArr);
        return $this->sendJsonResponse($response);
    }


    /**
     * get sub services of of the given parent services
     * @return type
     */
    public function bookService(Request $request) {
        $parentServiceIdArr = $request->input('parentServiceIdArr');
        $response = BeauticianServiceProvider::getSubServices($parentServiceIdArr);
        return $this->sendJsonResponse($response);
    }

}
