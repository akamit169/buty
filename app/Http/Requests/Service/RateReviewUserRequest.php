<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: RateReviewUserRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit kumar
 * CreatedOn: date (12/05/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;
use App\Models\User;

class RateReviewUserRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
          'bookingId' => 'required|validateUserBookingId|validateUserRating',
          'userId' => 'required|exists:users,id',
          'rating' => 'required|integer|digits_between: 1,5|validateUserRatingPoint',
          'comment' => 'required_if:'.\Auth::user()->user_type.',==,'.User::IS_CUSTOMER,
          'reasonId' => 'sometimes|integer|validateReasonId'
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

}
