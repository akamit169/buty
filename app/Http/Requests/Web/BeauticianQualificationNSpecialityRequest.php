<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianQualificationNSpecialityRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (06/06/2018) 
 */

namespace App\Http\Requests\Web;
use App\Http\Requests\BaseApiRequest;

class BeauticianQualificationNSpecialityRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'qualification' => 'min:1|max:255|required_without:speciality|validateQualificationSpecialityName|validateQualificationDuplicacy',
            'speciality' => 'min:1|max:255|required_without:qualification|validateQualificationSpecialityName|validateSpecialityDuplicacy'
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
