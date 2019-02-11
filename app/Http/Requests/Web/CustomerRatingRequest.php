<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: CustomerRatingRequest.php
 * CodeLibrary/Project: BeautyJunkie
 * Author:Amit kumar
 * CreatedOn: date (17/07/2018) 
 */

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRatingRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'bookingId' => 'required|integer|bookingId',
            'userId' => 'required|integer|customerIdForBooking'
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
