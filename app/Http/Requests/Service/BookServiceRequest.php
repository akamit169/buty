<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BookServiceRequest.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (12/05/2018) 
 */

namespace App\Http\Requests\Service;
use App\Http\Requests\BaseApiRequest;

use App\Models\User;
use \App\Utilities\DateTimeUtility;


class BookServiceRequest extends BaseApiRequest {


       protected $rules = [
          'beauticianId' => 'required',
          'bookingAddress' => 'required',
          'travelCost' => 'sometimes',
          'timezone' => 'required',
          'distance' => 'required',
          'bookingArr' => 'required|validateSlotOverlap'
        ];

        protected $fieldRules = 
           [
            'serviceId' => 'required',
            'serviceName' => 'required',
            'parentServiceId' => 'required',
            'parentServiceName' => 'required',
            'bookingNote' => 'sometimes',
            'startDateTime' => 'required|date_format:Y-m-d H:i:s|validateBookingSlotAvailability',
            'localStartDateTime' => 'required',
            'duration' => 'required',
            'serviceCost' => 'required',
            'discount' => 'required',
            'actualCost' => 'required',
            'hasMultipleSessions' => 'required|in:0,1',
            'sessionNo' => 'required',
            'onSiteService' => 'required|in:0,1',
            'naturalImage' => 'sometimes|image|mimes:jpg,png,jpeg',
            'aspirationImage' => 'sometimes|image|mimes:jpg,png,jpeg',
            'discount' => 'required'
        ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {


        $bookingArr = $this->request->get('bookingArr');

        foreach($bookingArr as $key => $value)
        {

          foreach ($this->fieldRules as $ruleKey => $ruleVal) {

               if($ruleKey == 'startDateTime')
               {
                $ruleVal .= ':'.$key; 
               }

               $this->rules['bookingArr.'.$key.'.'.$ruleKey] = $ruleVal;
          }

          //this code needs to be removed // done for unsed variable
          if($value == 0){ return;}
         
        }

        return $this->rules;
    }



     /**
     * @return array
     */
    public function messages() {


         foreach ($this->get('bookingArr') as $key => $val) {
            $messages["bookingArr.$key.startDateTime.validate_booking_slot_availability"] = 
            "This slot :- '".DateTimeUtility::convertDateTimeToTimezone($val['startDateTime'],$this->get('timezone'),'d/m/Y h:i:s a')."' is not available";
        }

        return $messages;

       
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
