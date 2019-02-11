<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BaseApiRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Http\Requests;

use Symfony\Component\HttpFoundation\Response;

/**
 * This class is base class for all API request
 * @author Shyam
 */
class BaseApiRequest extends Request {

    protected $response = null;

    /**
     * Get data to be validated from the request.
     * This method is used to get json input for APIs and validate the data
     * @return array
     */
    protected function validationData() {
        $postData = Request::all();
        return $postData;
    }

    /**
     * This method is used to send custom response when validation fails
     * @param array $errors
     * @return type
     */
    public function response(array $errors) {
        $firstError = '';
        foreach ($errors as $error) {
            $firstError = $error[0];
            break;
        }
        $this->response['message'] = $firstError;
        $this->response['errors'] = $errors;
        $this->response['success'] = false;

        return \Illuminate\Support\Facades\Response::json($this->response, Response::HTTP_BAD_REQUEST)->header('Content-Type', "application/json");
    }

}
