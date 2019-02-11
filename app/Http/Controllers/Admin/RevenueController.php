<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: RevenueController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit kumar
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Providers\Admin\RevenueServiceProvider;
use Illuminate\Support\Facades\Request;

class RevenueController extends BaseController {

    /**
     * used to get list of revenue gain month by month
     *
     * @return void
     */
    public function getRevenueGain() {
        try {
            $formattedMonthArray = array(
                    "1" => "January", "2" => "February", "3" => "March", "4" => "April",
                    "5" => "May", "6" => "June", "7" => "July", "8" => "August",
                    "9" => "September", "10" => "October", "11" => "November", "12" => "December",
                );
            return view('admin.revenue.revenue_gain')->with('formattedMonthArray', $formattedMonthArray);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * used to get list of revenue gain month by month
     * @return json
     */
    public function getRevenueGainListAjax() {

        try {
            if (Request::ajax()) {
                $arrMonthWiseRevenueGain = RevenueServiceProvider::getRevenueGainList();
                return $arrMonthWiseRevenueGain;
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }
    
    /**
     * used to get booking ratio month by month of a year
     *
     * @return void
     */
    public function getBookingRatio() {
        try {
            return view('admin.revenue.booking_ratio');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * used to get booking ratio month by month of a year
     * @return json
     */
    public function getBookingRatioListAjax() {

        try {
            if (Request::ajax()) {
                $arrMonthWiseBooking = RevenueServiceProvider::getBookingRatioList();
                return $arrMonthWiseBooking;
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }
    
    /**
     * used to get list of used service
     *
     * @return void
     */
    public function getUsedServiceList() {
        try {
            return view('admin.revenue.used_service_list');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * function is used to get list of used service
     * @return json
     */
    public function getUsedServiceListAjax() {

        try {
            if (Request::ajax()) {
                $arrMonthWiseBooking = RevenueServiceProvider::getUsedServiceList();
                return $arrMonthWiseBooking;
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }
    
    /**
     * used to get list of upcoming revenue
     *
     * @return void
     */
    public function getUpcomingRevenueList() {
        try {
            return view('admin.revenue.upcoming_revenue_list');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }

    /**
     * function is used to get list of upcoming revenue
     * @return json
     */
    public function getUpcomingRevenueListAjax() {

        try {
            if (Request::ajax()) {
                $arrMonthWiseBooking = RevenueServiceProvider::getUpcomingRevenueGainList();
                return $arrMonthWiseBooking;
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }
    
    public function getRepeatedUserList() {
        try {
            return view('admin.revenue.repeated_user_list');
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            return Redirect::back();
        }
    }
    
    public function getRepeatedUserListAjax() {
        try {
            if (Request::ajax()) {
                $arrMonthWiseBooking = RevenueServiceProvider::getRepeatedUserListMonthWise();
                return $arrMonthWiseBooking;
            }
            abort(404);
        } catch (Exception $e) {
            Log::error(__CLASS__ . "::" . __METHOD__ . ' ' . $e->getFile() . $e->getLine() . $e->getMessage());
            abort(404);
        }
    }

}
