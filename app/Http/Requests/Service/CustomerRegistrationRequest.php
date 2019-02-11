<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: UserRegistrationRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;
use App\Models\User;
use App\Models\UserDevice;

class CustomerRegistrationRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email' => 'required|email',
            'facebookId' => 'sometimes',
            'firstName' => 'required|string|max:200|regex:/(?=.*[a-zA-Z0-9])/',
            'lastName' => 'required|string|max:200|regex:/(?=.*[a-zA-Z0-9])/',
            'password' => 'required_without:facebookId|max:12|string|regex:/(?=.*[a-zA-Z0-9])(?=.*[!$#%@])/|same:confirmPassword|min:4',
            'confirmPassword' => 'sometimes|max:12|min:4',
            'userType' => 'required|in:'.User::IS_CUSTOMER,
            'deviceToken' => 'required',
            'deviceType' => 'required|in:'.UserDevice::IS_IOS,
            'referralCode' => 'sometimes'
        ];
    }

    /**
     * @return array
     */
    public function messages() {
        return [
            'password.min' => 'Password should be minimum 4 characters and maximum 12 characters containing at least one special character (!, $, #,%,@).',
            'password.max' => 'Password should be minimum 4 characters and maximum 12 characters containing at least one special character (!, $, #,%,@).',
            'password.string' => 'Password should be minimum 4 characters and maximum 12 characters containing at least one special character (!, $, #,%,@).',
            'password.regex' => 'Password should be minimum 4 characters and maximum 12 characters containing at least one special character (!, $, #,%,@).',
            'firstName.regex' => 'First Name should be in characters or digits.',
            'lastName.regex' => 'Last Name should be in characters or digits.'
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
