<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianPortfolioRequest.php
 * CodeLibrary/Project: BeautyJunkie
 * Author:Amit Yadav
 * CreatedOn: date (06/06/2018) 
 */

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class BeauticianPaymentDetail extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'cardToken' => 'required_if:accountNo,==,""',
            'accountNo' => 'required_if:cardToken,==,""'
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
