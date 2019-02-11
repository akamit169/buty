<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: ServiceBookingController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (11/05/2018) 
 */

namespace App\Http\Controllers\Service;

use App\Http\Controllers\BaseController;

use App\Providers\BookingServiceProvider;
use App\Http\Requests\Service\BookServiceRequest;
use App\Http\Requests\Service\RateReviewUserRequest;
use App\Http\Requests\Service\MarkServiceCompleteRequest;
use App\Http\Requests\Service\CancelBookingRequest;
use Illuminate\Http\Request;


class ServiceBookingController extends BaseController {

    /**
     * get sub services of of the given parent services
     * @return type
     */
    public function bookService(BookServiceRequest $request) {
        if(env('LOG_BOOKING_REQUEST') == 1)
        {
            \Log::info("\n\nAPI:: bookService[".date('Y-m-d H:i:s')."] AccessToken:: ".$request->header("accessToken")." Request:: ".json_encode($request->all()));
        }
        
        $requestObj = $request->all();
        $response = BookingServiceProvider::bookService($requestObj);
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to rate customer and beutician pro once service is completed
     * @param RateReviewUserRequest $request
     * @return type
     */
    public function postRateReviewUser(RateReviewUserRequest $request) {
        $input = $request->all();
        $response = BookingServiceProvider::rateReviewUser($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to mark a service complete
     * @param MarkServiceCompleteRequest $request
     * @return type
     */
    public function postMarkServiceComplete(MarkServiceCompleteRequest $request) {
        $input = $request->all();
        $response = BookingServiceProvider::markServiceComplete($input);
        return $this->sendJsonResponse($response);
    }

     /**
     * function is used to get user bookings
     * @return type
     */
    public function getUserBookings(){
        $response = BookingServiceProvider::getUserBookings();
        return $this->sendJsonResponse($response);
    }

     /**
     * function is used to cancel a booking
     * @param CancelBookingRequest $request
     * @return type
     */
    public function cancelBooking(CancelBookingRequest $request) {
        $response = BookingServiceProvider::cancelBooking($request->input('bookingId'));
        return $this->sendJsonResponse($response);
    }

    public function getUserPendingFeedback() {
        $response = BookingServiceProvider::getUserPendingFeedback();
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to get booking summary of a user
     * @param CancelBookingRequest $request
     * @return type
     */
    public function getBookingSummary(Request $request) {
        $response = BookingServiceProvider::getBookingSummary($request->input('bookingId'));
        return $this->sendJsonResponse($response);
    }

    
}
