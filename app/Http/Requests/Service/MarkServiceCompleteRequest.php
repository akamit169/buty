<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: MarkServiceCompleteRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (22/06/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;
use \App\Models\CustomerBooking;


class MarkServiceCompleteRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
          'bookingId' => 'required|integer|exists:customer_bookings,id,status,'.CustomerBooking::IS_PENDING,
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
