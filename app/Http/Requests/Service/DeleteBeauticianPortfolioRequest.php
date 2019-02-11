<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: DeleteBeauticianPortfolioRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (14/04/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;

class DeleteBeauticianPortfolioRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'portfolioId' => 'required|integer|validateBeauticianPortfolioId'
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
