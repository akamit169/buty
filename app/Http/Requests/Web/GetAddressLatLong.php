<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: SetupBusinessProfileRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (11/06/2018) 
 */

namespace App\Http\Requests\Web;
use App\Http\Requests\Request;

class GetAddressLatLong extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'address' => 'sometimes',
            'suburb' => 'required',
            'country' => 'required'
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
