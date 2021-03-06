<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: SetupBusinessProfileRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (12/04/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;
use App\Models\User;


class SetupBusinessProfileRequest extends BaseApiRequest {

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
            'phone' => 'required|digits_between:8,15',
            'instaId' => 'sometimes',
            'postalCode' => 'sometimes|digits:4',
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
