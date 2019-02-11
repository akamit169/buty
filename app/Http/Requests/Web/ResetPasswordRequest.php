<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: PostResetPasswordRequest.php
 * CodeLibrary/Project: BeautyJunkie
 * Author:Amit Yadav
 * CreatedOn: date (06/04/2018) 
 */

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
           'password' => 'required|max:12|string|regex:/(?=.*[a-zA-Z0-9])(?=.*[!$#%@])/|same:confirmPassword|min:4',
           'confirmPassword' => 'required|max:12',
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
