<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: DeleteBeauticianKitRequest.php
 * CodeLibrary/Project: BeautyJunkie
 * Author:Amit kumar
 * CreatedOn: date (06/06/2018) 
 */

namespace App\Http\Requests\Web;
use App\Http\Requests\BaseApiRequest;

class DeleteBeauticianKitRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'deletedKitId' => 'required|validateKitName'
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
