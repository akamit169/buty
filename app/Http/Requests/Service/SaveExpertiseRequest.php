<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: SaveExpertiseRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (15/04/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;

class SaveExpertiseRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'qualification' => 'max:500',
            'deletedQualificationId' => 'sometimes',
            'speciality' => 'max:500',
            'deletedSpecialityId' => 'sometimes'
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
