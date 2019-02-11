<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianPortfolioRequest.php
 * CodeLibrary/Project: BeautyJunkie
 * Author:Amit kumar
 * CreatedOn: date (12/04/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;

class BeauticianPortfolioRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'portfolioPic' => 'required|image|mimes:jpg,png,jpeg|max:'.(1024*5), //5 MB
            'serviceId' => 'required|integer|exists:services,id|validateImageCount'
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
