<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: SetupCustomerProfileRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (13/04/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;

use App\Models\User;


class SetupCustomerProfileRequest extends BaseApiRequest {

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
         'gender' => 'required|in:'.implode(',',User::GENDER),
         'dateOfBirth' => 'date_format:Y-m-d',
         'lat' => 'required',
         'lng' => 'required',
         'skinColorId' => 'required',
         'skinTypeId' => 'required',
         'hairTypeId' => 'required',
         'hairlengthTypeId' => 'required',
         'isHairColored' => 'required',
         'allergies' => 'sometimes',
         'postalCode' => 'required|numeric',
         'phone' => 'required|numeric',
         'description' => 'required|string|max:500'
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
