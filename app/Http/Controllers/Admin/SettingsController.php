<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: SettingsController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (09/08/2018) 
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Redirect;
use App\Providers\Admin\UserServiceProvider;
use App\Providers\BookingServiceProvider;

class SettingsController extends Controller {

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
    public function getAdminSettings() {
        try {
            return view('admin.settings');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * function is used to get customer list
     * @return json
     */
    public function getAdminSettingsListAjax(Request $request) {

        try {
            if (RequestFacade::ajax()) {
                return UserServiceProvider::getSettings($request->all());
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }
    


    /**
     * function is used to modify app settings
     * @return type
     */
    public function modifyAdminSetting(Request $request) {
        try {

            if (RequestFacade::ajax()) {
                return UserServiceProvider::modifyAdminSetting($request->all());
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }



    /**
     * used to get commission settings view
     *
     * @return void
     */
    public function getCommissionSettings() {
        try {
            return view('admin.commission-settings');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }


    /**
     * used to get states of the beautician
     *
     * @return void
     */
    public function getStates() {
        try {
            
            if (RequestFacade::ajax()) {
            $response = UserServiceProvider::getStates();
            return $response;
           }
           abort(404);

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }


    /**
     * used to save state commissions
     *
     * @return void
     */
    public function saveStateCommissions(Request $request) {
        try {
            
            if (RequestFacade::ajax()) {
              $response = UserServiceProvider::saveStateCommissions($request->all());
              return $response;
           }
           abort(404);

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }


    /**
     * used to get service with their commission percent
     * @return void
     */
    public function getServiceCommissions() {
        try {
            if (RequestFacade::ajax()) {
            $response = UserServiceProvider::getServiceCommissions();
            return $response;
        }
        abort(404);

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

     /**
     * used to get service with their commission percent
     * @return void
     */
    public function getBeauticianServiceCommissions(Request $request) {
        try {
            
            if (RequestFacade::ajax()) {
             $response = UserServiceProvider::getBeauticianServiceCommissions($request->input('beauticianId'));
             return $response;
          }
          abort(404);

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    


    /**
     * used to save service commission percent
     *
     * @return void
     */
    public function saveServiceCommissions(Request $request) {
        try {
            if (RequestFacade::ajax()) {
            $response = UserServiceProvider::saveServiceCommissions($request->all());
            return $response;
        }
        abort(404);

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

     /**
     * used to save premium service commission percent
     *
     * @return void
     */
    public function savePremiumServiceCommissions(Request $request) {
        try {
            if (RequestFacade::ajax()) {
            $response = UserServiceProvider::savePremiumServiceCommissions($request->all());
            return $response;
        }
        abort(404);

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }


    /**
     * used to remove premium service commission percent
     *
     * @return void
     */
    public function removePremiumCommissions(Request $request) {
        try {

            if (RequestFacade::ajax()) {
            $response = UserServiceProvider::removePremiumCommissions($request->all());
            return $response;
         }
         abort(404);

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    
    /**
     * used to get a global percentage for beauty pros
     *
     * @return void
     */
    public function getBeautyProGlobalPercent() {
        try {
            if (RequestFacade::ajax()) {
            $response = UserServiceProvider::getBeautyProGlobalPercent();
            return $response;
        }
        abort(404);

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * used to set a global percentage for beauty pros
     *
     * @return void
     */
    public function setBeautyProGlobalPercent(Request $request) {
        try {
            if (RequestFacade::ajax()) {
            $response = UserServiceProvider::setBeautyProGlobalPercent($request->all());
            return $response;
        }
        abort(404);

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    

     /**
     * used to set a global premium percentage for beauty pros
     *
     * @return void
     */
    public function setBeautyProPremiumGlobalPercent(Request $request) {
        try {
            if (RequestFacade::ajax()) {
            $response = UserServiceProvider::setBeautyProPremiumGlobalPercent($request->all());
            return $response;
        }
        abort(404);

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    


    /**
     * used to get the listing of all the beauty pros
     * @return void
     */
    public function getBeautyProListing() {
        try {
            if (RequestFacade::ajax()) {
            $response = UserServiceProvider::getBeautyProListing();
            return $response;
        }
        abort(404);

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }


    /**
     * used to get the listing of all the premium beauty pros
     * @return void
     */
    public function getPremiumBeautyPros() {
        try {
            if (RequestFacade::ajax()) {
            $response = UserServiceProvider::getPremiumBeautyPros();
            return $response;
        }
        abort(404);

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }



  
    public function testCommission() {
        try {
            
            $response = BookingServiceProvider::getCommissionPercent(49,3);
            return $response;

        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }


    

    



    

}
