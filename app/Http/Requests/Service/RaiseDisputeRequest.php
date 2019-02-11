<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: RaiseDisputeRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (20/06/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;
use App\Models\User;

class RaiseDisputeRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
          'customerId' => 'required|exists:users,id,user_type,'.User::IS_CUSTOMER,
          'beauticianId' => 'required|exists:users,id,user_type,'.User::IS_BEAUTICIAN,
          'bookingId' => 'required|exists:customer_bookings,id',
          'reason' => 'required|max:5000'
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
