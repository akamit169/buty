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
use App\Models\User;

use Illuminate\Validation\Rule;


class SetupBusinessProfileRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'address' => 'sometimes',
            'suburb' => 'required',
            'state' => 'required',
            'country' => 'required',
            'workRadius' => 'sometimes|numeric',
            'lat' => 'required',
            'lng' => 'required',
            'phone' => 'required|numeric',
            'instaId' => 'sometimes',
            'postalCode' => 'sometimes|numeric',
            'profilePic' => 'image',
            'crueltyFreeMakeup' => 'sometimes|in:'.User::IS_CRUELTY_FREE.', '.User::IS_NOT_CRUELTY_FREE
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
