<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: SignUpRequest.php
 * CodeLibrary/Project: BeautyJunkie
 * Author:Amit
 * CreatedOn: date (06/04/2018) 
 */

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'firstName' => 'required|string|max:200|regex:/^[a-zA-Z0-9\s-]+$/',
            'lastName' => 'required|string|max:200|regex:/^[a-zA-Z0-9\s-]+$/',
            'businessName' => 'required|regex:/(?=.*[a-zA-Z0-9])/',
            'abn' => 'required|digits:11',
            'phone' => 'required|digits_between:8,15',
            'instaId' => 'sometimes',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|max:12|string|regex:/(?=.*[a-zA-Z0-9])(?=.*[!$#%@])/|same:confirmPassword|min:4',
            'confirmPassword' => 'required|max:12|min:4',
            'certificate' => 'required|image|mimes:jpg,png,jpeg'
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
            'businessName.regex' => 'Business Name should be in characters or digits.',
            'phone.required' => 'The mobile field is required.',
            'phone.numeric' => 'The mobile field should be a number.',
            'certificate.required' => 'You must attach your police check certificate.'
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
