<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianBookingController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit yadav
 * CreatedOn: date (17/07/2018) 
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Providers\BookingServiceProvider;
use App\Providers\CustomerServiceProvider;
use App\Providers\PaymentServiceProvider;
use App\Providers\UserServiceProvider;
use Illuminate\Http\Request;
use \App\Models\User;

class BeauticianBookingController extends BaseController {
    
    /**
     * function is used to get beautician already created kit and view to add more kit
     * @return type
     */
    public function getBookings() {
       return view('beautician.bookings');
    }

     /**
     * function is used to get beautician booking details view
     * @return type
     */
    public function getBookingDetails(Request $request) {
       $bookingId = $request->input('id');
       $details = BookingServiceProvider::getBookingSummary($bookingId);
       $cardDetail = PaymentServiceProvider::fetchDefaultCardDetail(User::find($details['bookingDetails']['customer_id'])->stripe_customer_id);
       return view('beautician.booking-summary')->with('bookingDetails',$details)->with('cardDetail',$cardDetail);
    }

    /**
     * function is used to get booking customer profile
     * @return type
     */
    public function getCustomerProfile(Request $request) {
       $customerId = $request->input('id');
       $details = CustomerServiceProvider::getCustomerDetails($customerId); 
       return view('beautician.booking-customer-profile')->with('response',$details);
    }


    /**
     * function is used to get the view for raising a dispute
     * @return type
     */
    public function getRaiseDispute(Request $request) {
       $bookingId = $request->input('id');
       $details = BookingServiceProvider::getBookingSummary($bookingId);
       return view('beautician.raise-dispute')->with('bookingDetails',$details);
    }

    /**
     * function is used to get the view for flagging a customer
     * @return type
     */
    public function getFlagCustomer(Request $request){
        $response = UserServiceProvider::getFlagReasons(User::IS_BEAUTICIAN);
        return view('beautician.flag-customer')->with('reasons',$response['reasons']);
    }


       /**
     *function is used for flagging a customer
     * @return type
     */
    public function postFlagCustomer(Request $request){
       $response = UserServiceProvider::flagUser($request->all()); 
        if ($response['success'] == true) {
            return redirect()->back()->with('success_msg', $response['message']);
        } else {
            return redirect()->back()->with('error_msg', $response['message'])->withInput();
        }
    }
    
    /**
     * function is used to store image to share temporary
     * @param Request $request
     */
    public function postShareImage(Request $request) {
        $response = UserServiceProvider::uploadFileToTemp($request);
        return back()->with('fileName',$response->getFilename());
    }

}
