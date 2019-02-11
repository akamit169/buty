<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: AdminController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit kumar
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Providers\Admin\UserServiceProvider;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Http\Requests\Admin\TemporaryPasswordRequest;
use Auth;

class AdminController extends Controller {

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
    
    /*
     * get admin dashboard
     */
    public function getDashboard() {
        return view('admin.dashboard');
    }

    /**
     * logout
     *
     * @return void
     */
    public function getLogout() {
        Auth::logout();
        return redirect('auth/login');
    }

    /**
     * used for changed password
     *
     * @return void
     */
    public function getChangePassword() {
        return view('admin.change-password');
    }

    /**
     * used for changed password
     *
     * @return void
     */
    public function getChangePasswordByDasboard() {

        return view('admin.user.changed-password');
    }

    /**
     * used for changed password
     *
     * @return void
     */
    public function postChangePassword(ChangePasswordRequest $request) {

        $response = UserServiceProvider::changePassword($request->all());
        if ($response['success'] == true) {
            return redirect('admin/user');
        } else {
            return redirect()->back()->with('error_msg', $response['message']);
        }
    }

    /**
     * used for changed password
     *
     * @return void
     */
    public function postChangePasswordByDasboard(ChangePasswordRequest $request) {

        $response = UserServiceProvider::changePassword($request->all());
        if ($response['success'] == true) {

            return redirect()->back()->with('success_msg', $response['message']);
        } else {
            return redirect()->back()->with('error_msg', $response['message']);
        }
    }

    /**
     * send TemporaryPassword
     *
     * @return void
     */
    public function getTemporaryPassword() {

        return view('admin.forget-password');
    }

    /**
     * send TemporaryPassword
     *
     * @return void
     */
    public function postTemporaryPassword(TemporaryPasswordRequest $request) {

        $response = UserServiceProvider::temporaryPassword($request->all());
        if ($response['success'] == true) {
            return redirect('auth/login')->with('success_msg', $response['message']);
        } else {
            return redirect()->back()->with('error_msg', $response['message']);
        }
    }

}
