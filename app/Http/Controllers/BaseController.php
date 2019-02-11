<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BaseController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;

class BaseController extends Controller {

   
    public function sendJsonResponse($response) {
        return \Illuminate\Support\Facades\Response::json($this->convertToCamelCase($response), $response['status_code'])->header('Content-Type', "application/json");
    }

    /**
     * Convert to Camel Case
     *
     * Converts array keys to camelCase, recursively.
     * @param  array  $array Original array
     * @return array
     */
    protected function convertToCamelCase($array) {
        $convertedArray = [];
        foreach ($array as $oldKey => $value) {
            if (is_array($value)) {
                $value = $this->convertToCamelCase($value);
            } else if (is_object($value)) {
                if ($value instanceof Model || method_exists($value, 'toArray')) {
                    $value = $value->toArray();
                } else {
                    $value = (array) $value;
                }

                $value = $this->convertToCamelCase($value);
            }
            $convertedArray[camel_case($oldKey)] = $value;
        }

        return $convertedArray;
    }

}
