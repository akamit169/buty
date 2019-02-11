<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: RevenueServiceProvider.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Providers\Admin;

use App\Providers\BaseServiceProvider;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerBooking;

/**
 * RevenueServiceProvider class contains methods for user management
 * 
 */
 
class RevenueServiceProvider extends BaseServiceProvider {

    /**
     * get Revenue gain month by month for a year
     * @return type
     */
    public static function getRevenueGainList() {
        $input = Input::all();
        $year = $input['year'];

        
        $monthWiseRevenueGain = CustomerBooking::whereIn('status', [CustomerBooking::IS_PAYMENT_DONE, CustomerBooking::IS_DISPUTED_PAYMENT_DONE])
                                            ->whereRaw('YEAR(start_datetime) = '.$year)
                                            ->select(DB::raw('SUM(actual_cost + travel_cost) as total_cost'), 
                                                    DB::raw('MONTH(start_datetime) as month'))
                                            ->groupBy('month')->orderBy('month')->get()->toArray();
        $arrMonthWiseRevenueGain = [];
        for($i=1; $i<=12; $i++) {
            foreach($monthWiseRevenueGain as $value) {
                if($i == $value['month']) {
                    $totalCost = (int) ceil($value['total_cost']);

                    $arrMonthWiseRevenueGain[$i] = $totalCost;
                } else if(!array_key_exists($i, $arrMonthWiseRevenueGain)){
                    $arrMonthWiseRevenueGain[$i] =  0.00;
                }
            }
        }
        return $arrMonthWiseRevenueGain;
    }
    
    /**
     * get booking ratio month by month for a year
     * @return type
     */
    public static function getBookingRatioList() {
        $input = Input::all();
        $year = $input['year'];
        CustomerBooking::whereRaw('YEAR(start_datetime) = '.$year)
                                ->select(DB::raw('COUNT(id) as total_booking'))->first();
        
        $monthWiseBooking = CustomerBooking::whereRaw('YEAR(start_datetime) = '.$year)
                                            ->select(DB::raw('COUNT(id) as total_booking'), 
                                                    DB::raw('MONTH(start_datetime) as month'))
                                            ->groupBy('month')->orderBy('month')->get()->toArray();
        $arrMonthWiseBooking = [];
        for($i=1; $i<=12; $i++) {
            foreach($monthWiseBooking as $value) {
                if($i == $value['month']) {
                    $totalBooking = (int) $value['total_booking'];
                    $arrMonthWiseBooking[$i] = $totalBooking;
                } else if(!array_key_exists($i, $arrMonthWiseBooking)){
                    $arrMonthWiseBooking[$i] =  0.00;
                }
            }
        }
        return $arrMonthWiseBooking;
    }
    
    /**
     * get service booking ratio for a year
     * @return type
     */
    public static function getUsedServiceList() {
        $input = Input::all();
        $year = $input['year'];
        
        $monthWiseServiceBooking = CustomerBooking::join('services', 'services.id', '=', 'customer_bookings.parent_service_id')
                                ->whereRaw('YEAR(start_datetime) = '.$year)
                                ->select(DB::raw('customer_bookings.id as total_booking'),
                                        'services.name')
                                ->groupBy('customer_bookings.parent_service_id')
                                ->orderBy('customer_bookings.parent_service_id')
                                ->get()->toArray();
        
        return $monthWiseServiceBooking;
    }
    
    /**
     * get Upcoming Revenue gain month by month for a year
     * @return type
     */
    public static function getUpcomingRevenueGainList() {
        $input = Input::all();
        $year = $input['year'];
        
        $monthWiseRevenueGain = CustomerBooking::whereIn('status', [CustomerBooking::IS_DONE_PAYMENT_LEFT, CustomerBooking::PAYMENT_HELD])
                                            ->whereRaw('YEAR(start_datetime) = '.$year)
                                            ->select(DB::raw('SUM(actual_cost + travel_cost) as total_cost'), 
                                                    DB::raw('MONTH(start_datetime) as month'))
                                            ->groupBy('month')->orderBy('month')->get()->toArray();
        $arrMonthWiseRevenueGain = [];
        for($i=1; $i<=12; $i++) {
            foreach($monthWiseRevenueGain as $value) {
                if($i == $value['month']) {
                    $arrMonthWiseRevenueGain[$i] = (int) ceil($value['total_cost']);
                } else if(!array_key_exists($i, $arrMonthWiseRevenueGain)){
                    $arrMonthWiseRevenueGain[$i] =  0;
                }
            }
        }
        return $arrMonthWiseRevenueGain;
    }
    
    /**
     * get repeated user list month wise
     * @return type
     */
    public static function getRepeatedUserListMonthWise() {
        $input = Input::all();
        $year = $input['year'];
        
        $monthWiseCustomer = CustomerBooking::whereRaw('YEAR(start_datetime) = '.$year)
                                            ->select(DB::raw('COUNT(DISTINCT customer_id) as total_cout'), 
                                                    DB::raw('MONTH(start_datetime) as month'))
                                            ->groupBy('month')
                                            ->havingRaw('COUNT(customer_id) > 1')->orderBy('month')->get()->toArray();
        
        $arrMonthWiseCustomerCount = [];
        for($i=1; $i<=12; $i++) {
            foreach($monthWiseCustomer as $value) {
                if($i == $value['month']) {
                    $arrMonthWiseCustomerCount[$i] = (int) $value['total_cout'];
                } else if(!array_key_exists($i, $arrMonthWiseCustomerCount)){
                    $arrMonthWiseCustomerCount[$i] =  0;
                }
            }
        }
        return $arrMonthWiseCustomerCount;
    }
}
