<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: AuthBeautician.php
 * CodeLibrary/Project: NA/Beautyjunkie
 * Author:Amit
 * CreatedOn: date (10/04/2018) 
 */

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\BaseController;

class AuthBeautician {

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request            
     * @param \Closure $next            
     * @return mixed
     */
    public function handle($request, Closure $next) { 
        if (!\Auth::check()) {
            if ($request->ajax()) {
                $response['message'] = trans('messages.unauthorised');
                $response['success'] = false;
                $response['status_code'] = \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED;
                $baseController = new BaseController();
                return $baseController->sendJsonResponse($response);
            } else {
                return redirect('beautician/login');
            }
        }

        $feedbackDetail = \App\Models\CustomerBooking::getBeauticianPendingFeedback();

       
        if(!empty($feedbackDetail) && !\Illuminate\Support\Facades\Request::is('beautician/getCustomerRating') 
                && !\Illuminate\Support\Facades\Request::is('beautician/rateReviewUser')) {

             if ($request->ajax())
             {
                $response['success'] = false;
                $response['status_code'] = \Symfony\Component\HttpFoundation\Response::HTTP_OK;
                $response['redirect_url'] = url('beautician/rateReviewUser?bookingId='.$feedbackDetail->id.'&userId='.$feedbackDetail->customer_id);
                $baseController = new BaseController();
                return $baseController->sendJsonResponse($response);
             }
             else
             {
                return redirect('beautician/rateReviewUser?bookingId='.$feedbackDetail->id.'&userId='.$feedbackDetail->customer_id);
             }
            
        }

        
        return $next($request);
    }

}
