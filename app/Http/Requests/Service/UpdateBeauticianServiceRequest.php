<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: UpdateBeauticianServiceRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (27/04/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;

class UpdateBeauticianServiceRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'id' => 'required|integer|exists:beautician_services,id,deleted_at,NULL',
            'parentServiceId' => 'required|integer|exists:services,id',
            'serviceId' => 'required|integer|exists:services,id|validateBeauticianSubServiceId',
            'duration' => 'required|numeric',
            'cost' => 'required|numeric|between:0,99999',
            'description' => 'sometimes|string',
            'tip' => 'sometimes|string',
            'sessionNumber' => 'sometimes|digits_between:1,10',
            'timeBtwSession' => 'required_with:sessionNumber|numeric',
            'discount' => 'sometimes|numeric',
            'discountStartDate' => 'required_with:discount|date_format:Y-m-d H:i:s',
            'discountedDays' => 'required_with:discount|integer'
        ];
    }


     /**
     * @return array
     */
    public function messages() {
        return [
            'cost.between' => 'The price should be under 6 digits.'
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
