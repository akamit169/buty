<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BaseServiceProvider.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018)
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;

use \Symfony\Component\HttpFoundation\Response;
/**
 * BaseServiceProvider works as a base class for all user defined providers
 */
class BaseServiceProvider extends ServiceProvider {

    /**
     * The default response format returned by a service
     *
     * @var array
     * message key contains success/error message
     * success key contains true/false
     * errors key contains array of key-value validation errors for each input field in json, if validation fails
     * status_code key contains HTTP STATUS CODE based on response
     */
    protected static $data = [
        'message' => '',
        'success' => true,
        'errors' => [],
        'status_code' => Response::HTTP_OK
    ];
    protected static $mail_data = [
        'view' => '',
        'data' => [],
        'user' => [],
    ];
    protected static $mailData = [
        'view' => '',
        'data' => [],
        'user' => [],
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    public static function setExceptionError(\Exception $e,$msg=false) {

        if(!$msg)
        {
            $msg = trans('messages.exception_msg');
        }

        static::logExceptionMessage($e);
        
        static::$data['success'] = false;
        static::$data['status_code'] = Response::HTTP_INTERNAL_SERVER_ERROR;
        static::$data['message'] = $msg;
    }

    public static function setValidationError($message) {
        static::$data['success'] = false;
        static::$data['status_code'] = Response::HTTP_BAD_REQUEST;
        static::$data['message'] = $message;
    }

    public static function logExceptionMessage($e){
        \Log::error("\n\nThere is some exception in " . $e->getFile() . " on line no: " . $e->getLine() . " Message: " . $e->getMessage());
    }


    public static function logStripeExceptionMessage($e){ 
       \Log::error("\n\nThere is some exception in " . $e->getFile() . " on line no: " . $e->getLine() . " Message: " . $e->getMessage());
    }

    /**
     * function is used to delete array of images from s3
     * @param array $arrImages
     * @return boolean true/false
     */
    public static function deleteS3Images($arrImages) {
        $status = false;
        try {
            $status = \Storage::disk('s3')->delete($arrImages);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return $status;
    }
    
    /**
     * function is used to get list of all categories with its child categories
     * @param array $elements
     * @param int $parentId
     * @return array $branch
     */
    public static function buildTree(array $elements, $parentId = 0) {
        $branch = array();  
        
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = static::buildTree($elements, $element['id']);

                if ($children) {
                    $element['children'] = $children;
                }

                $branch[] = $element;
            }
        }

        return $branch;
    }
    
    /**
     * function is used to build booking tree based on multiple sessions
     * @param array $elements
     * @return array
     */
    public static function buildBookingTree(array $elements) {
        $branch = array();  
        
        foreach ($elements as $element) {
            if(array_key_exists($element['customer_bookings_master_id'], $branch)) {
                array_push($branch[$element['customer_bookings_master_id']]['bookings'], $element);
            } else {
                $branch[$element['customer_bookings_master_id']] = $element;
                $branch[$element['customer_bookings_master_id']]['bookings'][] = $element;
            }
        }
        $branch = array_values($branch);
        return $branch;
    }
    
    /**
     * function is used to get lat and long by address
     * @param string $address
     * @return array
     */
    public static function getLatLongByAddress($address) {
        $result = [];
        $url = \Config::get('constants.GOOGLE_PLACES_URL') . 'geocode/json?address=' . $address . '&key=' . env('API_KEY');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $responses = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($responses);
        if(!empty($response->results[0])){
           $location = $response->results[0]->geometry->location;
           $result['lat'] = $location->lat;
           $result['long'] = $location->lng;
        }
        return $result;
    }
}
