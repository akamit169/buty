<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: ChangePasswordRequest.php
 * CodeLibrary/Project: BeautyJunkie
 * Author:Amit kumar
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'old_password' => 'required|min:4',
            'password' => 'required|string|min:4|max:12|regex:/(?=.*[a-zA-Z0-9])(?=.*[!$#%@])/|confirmed|different:old_password',
            'password_confirmation' => 'required'
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
