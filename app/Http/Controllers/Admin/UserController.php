<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: UserController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit kumar
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Providers\Admin\UserServiceProvider;
use Illuminate\Support\Facades\Request;
use App\Models\User;

class UserController extends BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin.dashboard');
    }

    /**
     * function is used to export user
     */
    public function exportUser() {
        $header = ['Name', 'Email'];
        $filename = "users-list.csv";
        $response = UserServiceProvider::getUsers(User::IS_CUSTOMER);

        UserServiceProvider::getDataCsv($response['users']->toArray(), $header, $filename);
    }

    /**
     * function is used to export Beautician
     */
    public function exportBeautician($userStatus = 0) {
        $header = ['Name', 'Email'];
        $filename = "beautician-list.csv";
        $response = UserServiceProvider::getUsers(User::IS_BEAUTICIAN, $userStatus);

        UserServiceProvider::getDataCsv($response['users']->toArray(), $header, $filename);
    }

    /**
     * used to get list of all suspended user list
     *
     * @return void
     */
    public function getSuspendedUserList() {
        try {

            return view('admin.user.suspended_list');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * function is used to get suspended user list
     * @return json
     */
    public function getSuspendedUserListAjax() {

        try {
            if (Request::ajax()) {
                return UserServiceProvider::getSuspendedUserList();
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    /**
     * function is used to suspend or unsuspend the user
     * @param int $userId
     * @return type
     */
    public function getSuspendUnsuspendUser($userId) {
        try {
            $response = UserServiceProvider::suspendUnsuspendUser($userId);
            $userObj = $response['user'];
            if ($userObj->status == User::IS_ACTIVE && $userObj->user_type == User::IS_BEAUTICIAN) {
                if ($userObj->admin_approval_status == User::IS_APPROVAL_PENDING) {
                    $url = 'admin/beautician/get-beautician-list';
                } else if ($userObj->admin_approval_status == User::IS_APPROVED) {
                    $url = 'admin/beautician/approved-beautician-list';
                } else {
                    $url = 'admin/beautician/rejected-beautician-list';
                }
                return redirect($url)->with('message', trans('messages.beautician.unsuspended'))->with('status', true);
            } else if ($userObj->status == User::IS_ACTIVE && $userObj->user_type == User::IS_CUSTOMER) {
                return redirect('admin/customer/get-customer-list')->with('message', trans('messages.customer.unsuspended'))->with('status', true);
            } else {
                return redirect('admin/user/get-suspended-user-list')->with('message', trans('messages.user.suspended'))->with('status', true);
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    /**
     * used to get list of all flagged user list
     *
     * @return void
     */
    public function getFlaggedUserList() {
        try {

            return view('admin.user.flagged_list');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * function is used to get flagged user list
     * @return json
     */
    public function getFlaggedUserListAjax() {

        try {
            if (Request::ajax()) {
                return UserServiceProvider::getFlaggedUserList();
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    /**
     * function is used to fetch list of users reported to a given user 
     * @param int $id
     * @return type
     */
    public function getUserReportedByList($id) {
        try {
            $response = UserServiceProvider::getUserReportedByList($id);

            if (!$response['success']) {
                return redirect('admin/user/get-flagged-user-list')->withErrors(['status' => $response['message']]);
            }

            return view('admin.user.user_reported_by_list')->with('arrUser', $response['user']);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    /**
     * function is used to unflag an user
     * @param int $id
     * @return type
     */
    public function getUnflag($id) {
        try {
            $response = UserServiceProvider::unflagUser($id);
            if (!$response['success']) {
                return redirect('admin/user/get-flagged-user-list')->withErrors(['status' => $response['message']]);
            }

            return redirect('admin/user/get-flagged-user-list')->with('message', $response['message'])->with('status', true);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

    /**
     * used to get list of all referred user list
     *
     * @return void
     */
    public function getReferredUserList() {
        try {

            return view('admin.user.referred_user_list');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * function is used to get referred user list
     * @return json
     */
    public function getReferredUserListAjax() {

        try {
            if (Request::ajax()) {
                return UserServiceProvider::getReferredUserList();
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
    public function getCustomersRevenue() {
        try {
            
            $formattedMonthArray = array(
                    "1" => "January", "2" => "February", "3" => "March", "4" => "April",
                    "5" => "May", "6" => "June", "7" => "July", "8" => "August",
                    "9" => "September", "10" => "October", "11" => "November", "12" => "December",
                );
            $arrSuburb = UserServiceProvider::fetchUsersSuburb();
            return view('admin.user.customers_revenue_list')->with('formattedMonthArray', $formattedMonthArray)->with('arrSuburb', $arrSuburb);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }
    
    /**
     * function is used to get customers revenue
     * @return type
     */
    public function getCustomersRevenueListAjax($month='', $year='', $suburb='') {
        try {

            if (Request::ajax()) {
                return UserServiceProvider::getCustomerRevenue($month, $year, $suburb);
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
    public function getBeauticianRevenue() {
        try {
            
            $formattedMonthArray = array(
                    "1" => "January", "2" => "February", "3" => "March", "4" => "April",
                    "5" => "May", "6" => "June", "7" => "July", "8" => "August",
                    "9" => "September", "10" => "October", "11" => "November", "12" => "December",
                );
            $arrSuburb = UserServiceProvider::fetchUsersSuburb();
            return view('admin.user.beautician_revenue_list')->with('formattedMonthArray', $formattedMonthArray)->with('arrSuburb', $arrSuburb);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }
    
    /**
     * function is used to get customers revenue
     * @return type
     */
    public function getBeauticianRevenueListAjax($month='', $year='', $suburb='') {
        try {

            if (Request::ajax()) {
                return UserServiceProvider::getBeauticianRevenue($month, $year, $suburb);
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }
}
