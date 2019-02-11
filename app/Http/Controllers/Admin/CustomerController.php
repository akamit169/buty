<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: CustomerController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit kumar
 * CreatedOn: date (15/04/2018) 
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Providers\Admin\UserServiceProvider;

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
     * used to get list of all customer list
     *
     * @return void
     */
    public function getCustomerList() {
        try {

            return view('admin.customer.list');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * function is used to get customer list
     * @return json
     */
    public function getCustomerListAjax() {

        try {
            if (Request::ajax()) {
                return UserServiceProvider::getCustomerList();
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    /**
     * function is used to view customer
     * @param int $id
     */
    public function getViewCustomer($id) {

        try {
            $userObj = User::fetchCustomerDetail($id);
            if ($userObj) {
                return view('admin.customer.view_profile')->with('userObj', $userObj);
            } else {
                return redirect('admin/customer/get-customer-list')->withErrors(['status' => trans('messages.customer.not_exist')]);
            }
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    /**
     * function is used to view beautician
     * @param int $id
     */
    public function approveBeautician($id) {

        try {
            $status = User::approveBeautician($id);
            if ($status) {
                //send mail
                $arrStatus = UserServiceProvider::sendApprovedMail($id);
                if (isset($arrStatus) && $arrStatus['status']) {
                    return redirect('admin/beautician/get-beautician-list')->with('message', $arrStatus['message'])->with('status', true);
                } else {
                    return redirect('admin/beautician/get-beautician-list')->withErrors(['status' => $arrStatus['message']])->with('status', false);
                }
            } else {
                return redirect('admin/beautician/get-beautician-list')->withErrors(['status' => trans('messages.beautician.approved_failure')])->with('status', false);
            }
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    /**
     * function is used to view beautician
     * @param int $id
     */
    public function rejectBeautician($id) {

        try {
            $status = User::rejectBeautician($id);
            if ($status) {
                //send mail
                $arrStatus = UserServiceProvider::sendRejectedMail($id);
                if (isset($arrStatus) && $arrStatus['status']) {
                    return redirect('admin/beautician/get-beautician-list')->with('message', trans('messages.beautician.reject_success'))->with('status', true);
                } else {
                    return redirect('admin/beautician/get-beautician-list')->with('message', $arrStatus['message'])->with('status', false);
                }
            } else {
                return redirect('admin/beautician/get-beautician-list')->withErrors(['status' => trans('messages.beautician.reject_failure')])->with('status', false);
            }
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    /**
     * used to get list of approved business list
     *
     * @return void
     */
    public function getApprovedBeauticianList() {
        try {

            return view('admin.beautician.approved_list');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * function is used to get approved beautician list
     * @return json
     */
    public function getApprovedBeauticianListAjax() {

        try {
            if (Request::ajax()) {
                return UserServiceProvider::getApprovedBeauticianList();
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    /**
     * used to get list of rejected business list
     *
     * @return void
     */
    public function getRejectedBeauticianList() {
        try {

            return view('admin.beautician.rejected_list');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * function is used to get rejected beautician list
     * @return json
     */
    public function getRejectedBeauticianListAjax() {

        try {
            if (Request::ajax()) {
                return UserServiceProvider::getRejectedBeauticianList();
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

}
