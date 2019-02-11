<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianCurrentBookingRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (15/06/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;


class BeauticianCurrentBookingRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
          'startDateTime' => 'required|date_format:Y-m-d H:i:s',
          'showPastBookings' => 'required|in:0,1'
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
