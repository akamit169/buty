<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: UserProfilePicRequest.php
 * CodeLibrary/Project: BeautyJunkie
 * Author:Amit Yadav
 * CreatedOn: date (12/04/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;

class UserProfilePicRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'profilePic' => 'required|image|mimes:jpg,png,jpeg'
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
