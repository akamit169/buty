<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: CustomerController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit Yadav
 * CreatedOn: date (04/04/2018) 
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\UserServiceProvider;
use App\Http\Requests\Web\ResetPasswordRequest;

class CustomerController extends Controller {

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
     * customer reset password view
     * @param Request $request
     * @return type
     */
    public function getResetPassword(Request $request) {
        $input = $request->all();
        $response = UserServiceProvider::getUserByResetPasswordToken($input);
        return view('customer.reset-password', $response)->with('userType','customer');
    }
    
    /**
     * customer reset password
     * @param ResetPasswordRequest $request
     * @return type
     */
    public function postResetPassword(ResetPasswordRequest $request) {
        $input = $request->all(); 
        $response = UserServiceProvider::resetPassword($input);
        if ($response['success'] == true) {
            return view('customer.reset-password-success');
        } else {
            return redirect()->back()->with('error_msg', $response['message']);
        }
    }


      /*
     * get privacy policy view
     */

    public function getPrivacyPolicy() {
        return view('customer.privacy-policy');
    }

    /*
     * get terms and conditions view
     */

    public function getTermsAndConditions() {
        return view('customer.terms-and-conditions');
    }

}
