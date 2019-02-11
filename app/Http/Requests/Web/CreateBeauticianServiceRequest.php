<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: CreateBeauticianServiceRequest.php
 * CodeLibrary/Project: BeautyJunkie
 * Author:Amit Yadav
 * CreatedOn: date (09/06/2018) 
 */

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class CreateBeauticianServiceRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'parentServiceId' => 'required|integer|exists:services,id',
            'serviceId' => 'required|integer|exists:services,id|uniqueBeauticianService',
            'duration' => 'required|numeric',
            'cost' => 'required|numeric|between:0,99999',
            'description' => 'sometimes|string',
            'tip' => 'sometimes|string',
            'sessionNumber' => 'sometimes|digits_between:1,10',
            'timeBtwSession' => 'required_with:sessionNumber|numeric',
            'discount' => 'sometimes|numeric',
            'discountStartDate' => 'required_with:discount|date_format:Y-m-d H:i:s',
            'discountedDays' => 'required_with:discount|integer',
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
