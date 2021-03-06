<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianRegistrationRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (04/04/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;
use App\Models\User;
use App\Models\UserDevice;

class BeauticianRegistrationRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'firstName' => 'required|string|max:200|regex:/(?=.*[a-zA-Z0-9])/',
            'lastName' => 'required|string|max:200|regex:/(?=.*[a-zA-Z0-9])/',
            'password' => 'required|max:12|string|regex:/(?=.*[a-zA-Z0-9])(?=.*[!$#%@])/|same:confirmPassword|min:4',
            'confirmPassword' => 'required|max:12|min:4',
            'phone' => 'required',
            'businessName' => 'required|regex:/(?=.*[a-zA-Z0-9])/',
            'abn' => 'required|digits:11',
            'instaId' => 'sometimes',
            'userType' => 'required|in:'.User::IS_BEAUTICIAN,
            'deviceToken' => 'required',
            'deviceType' => 'required|in:'.UserDevice::IS_IOS,
            'certificate' => 'image|mimes:jpg,png,jpeg'
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
            'lastName.regex' => 'Last Name should be in characters or digits.',
            'businessName.regex' => 'Business Name should be in characters or digits.'
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
