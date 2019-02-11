<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: ServiceBookingController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit kumar
 * CreatedOn: date (07/07/2018) 
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Redirect;
use App\Providers\BookingServiceProvider;

class ServiceBookingController extends Controller {

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct() {
        //Log out Back
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
    }

    /**
     * used to get list of all job list
     *
     * @return void
     */
    public function getBookedServiceList() {
        try {

            return view('admin.service.booked-service-list');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * function is used to get customer list
     * @return json
     */
    public function getBookedServiceListAjax(Request $request) {

        try {
            if (RequestFacade::ajax()) {
                return BookingServiceProvider::getBookedServiceList($request->all());
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }
    
    /**
     * function is used to get customers revenue
     * @return type
     */
    public function getDisputedServiceList() {
        try {
            return view('admin.service.disputed-service-list');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }
    
    /**
     * function is used to get customers revenue
     * @return type
     */
    public function getDisputedServiceListAjax(Request $request) {
        try {

            if (RequestFacade::ajax()) {
                return BookingServiceProvider::getDisputedServiceList($request->all());
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    /**
     * function is used to resolve dispute on booking
     * @return type
     */
    public function resolveDispute(Request $request) {
        try {

            if (RequestFacade::ajax()) {
                return BookingServiceProvider::adminResolveDispute($request->all());
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    /**
     * function is used to reject dispute on booking
     * @return type
     */
    public function rejectDispute(Request $request) {
        try {

            if (RequestFacade::ajax()) {
                return BookingServiceProvider::adminRejectDispute($request->all());
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    

}
