<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: ChangePasswordRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Http\Requests;

class ChangePasswordRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
           'oldPassword' => 'required',
           'password' => 'required|max:12|string|regex:/(?=.*[a-zA-Z0-9])(?=.*[!$#%@])/|same:confirmPassword|min:4|different:oldPassword',
           'confirmPassword' => 'required',

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
            'password.regex' => 'Password should be minimum 4 characters and maximum 12 characters containing at least one special character (!, $, #,%,@).'
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
