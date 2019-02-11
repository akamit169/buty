<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: CustomValidator.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Http;

use App\Models\BeauticianPortfolio;
use App\Models\CustomerBooking;
use App\Models\BeauticianAvailabilitySchedule;
use DB;
use \App\Models\BeauticianService;
use \App\Utilities\DateTimeUtility;
use \App\Models\User;
use \App\Models\BookingRating;
use \App\Models\RatingReason;
use \App\Models\BeauticianFixhibition;
use \App\Models\BeauticianQualification;
use \App\Models\BeauticianKit;
use \App\Models\BeauticianSpeciality;

class CustomValidator {
    
    /**
     * function is used to validate if logged-in user has entered it own email id on feedback
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateCustomerPassword($attribute, $value, $parameters, $validator) {
        unset($attribute, $value, $parameters);
        $paramValues = $validator->getData();
        $facebookId = $paramValues['facebookId'];
        if(empty($facebookId) && !empty($facebookId)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * function is used to validate total number of image count to be added under one server for one user
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateImageCount($attribute, $value, $parameters, $validator) {
        unset($attribute, $value, $parameters);
        $paramValues = $validator->getData();
        $serviceId = $paramValues['serviceId'];
        
        if(!empty($serviceId)) {
            $userObj = \Auth::user();
            $imageCount = BeauticianPortfolio::getBeauticianServiceImageCount($userObj->id, $serviceId);
            $allowedImageCount = env('ALLOWED_IMAGE_UPLOAD_FOR_SERVICE');
            if($imageCount < $allowedImageCount) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * function is used to validate beautician portfolio id
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateBeauticianPortfolioId($attribute, $value, $parameters, $validator) {
        unset($attribute, $value, $parameters);
        $paramValues = $validator->getData();
        $portfolioId = $paramValues['portfolioId'];
        
        if(!empty($portfolioId)) {
            $userObj = \Auth::user();
            $portfolioObj = BeauticianPortfolio::where('user_id', $userObj->id)->where('id', $portfolioId)->first();
            
            if($portfolioObj) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * function is used to validate beautician kit name array
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateKitName($attribute, $value, $parameters, $validator) {
        unset($attribute, $value, $parameters);
        $paramValues = $validator->getData();
        $deletedKitId = (isset($paramValues['deletedKitId'])?array_filter($paramValues['deletedKitId']):array());
        $kitName = (isset($paramValues['kitName'])?array_filter($paramValues['kitName']):array());
        if(count($deletedKitId) == 0 && count($kitName) == 0) {
            
            return false;
        }
        
        return true;
    }
    
    /**
     * function is used to validate if service id is created by logged in beautician
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateBeauticianServiceId($attribute, $value, $parameters, $validator) {
        unset($attribute, $parameters, $validator);
        $userId = \Auth::user()->id;
        $beauticianServiceObj = BeauticianService::where('id', $value)
                                            ->where('beautician_id', $userId)->first();
        if($beauticianServiceObj) {
            return true;
        }
        return false;
    }
    
    /**
     * function is used to validate if service id is created by logged in beautician
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateBeauticianSubServiceId($attribute, $value, $parameters, $validator) {
        unset($attribute,$parameters);
        
        $userId = \Auth::user()->id;
        $paramValues = $validator->getData();
        
        $beauticianServiceObj = BeauticianService::where('service_id', $value)
                                            ->where('beautician_id', $userId)
                                            ->where('id', '!=', $paramValues['id'])->first();
        if($beauticianServiceObj) {
            return false;
        }
        return true;
    }
    
    /**
     * function is used to validate if all parameters are available in the request
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateBeauticianAvailabilityDetail($attribute, $value, $parameters, $validator) {
        unset($attribute,$parameters);
        $paramValues = $validator->getData();
        $arrAvailabilityDetail = $paramValues['arrAvailabilityDetails'];
        $status = true;
        
        foreach($arrAvailabilityDetail as $value) {

                 if(empty($value['startDatetime']) || empty($value['endDatetime']))
                 {
                    $status = false;
                 }
               
                if(!empty($value['startDatetime'])) {
                    $date = $value['startDatetime'];
                    $format = 'Y-m-d H:i:s';
                    if(date($format, strtotime($date)) != $date) {
                        $status = false;
                    }
                }
                if(!empty($value['endDatetime'])) {
                    $date = $value['endDatetime'];
                    $format = 'Y-m-d H:i:s';
                    if(date($format, strtotime($date)) != $date) {
                        $status = false;
                    }
                }
                if(!isset($value['isAvailable']) || ($value['isAvailable'] != BeauticianAvailabilitySchedule::IS_AVAILABLE 
                        && $value['isAvailable'] != BeauticianAvailabilitySchedule::IS_NOT_AVAILABLE)) {
                    $status = false;
                }
            }
        
        return $status;
    }
    
    /**
     * function is used to validate if same data exist on same date for same beautician or not
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateBeauticianAvailabilityExistence($attribute, $value, $parameters, $validator) {
        unset($attribute,$parameters);
        if(count($validator->errors()->all()) == 0){
            $userId = \Auth::user()->id;
            $paramValues = $validator->getData();
            $arrAvailabilityDetail = $paramValues['arrAvailabilityDetails'];
            $stringStartDt = '';
            foreach($arrAvailabilityDetail as $value) {
                $startDt = date('Y-m-d', strtotime($value['startDatetime']));
                $stringStartDt .= '"'.$startDt .'",';
            }
            $stringStartDt = rtrim($stringStartDt, ',');
            $availabilityExist = BeauticianAvailabilitySchedule::whereRaw('Date(start_datetime) in ('.$stringStartDt.')')->where('beautician_id',$userId)->first();
            
            if(!$availabilityExist) {
                return true;
            }
        }
        return false;
    }


     /**
     * function is used to validate if the slot being booked are not overlapping in terms of startdate and enddate
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateSlotOverlap($attribute, $value, $parameters, $validator) {

        unset($attribute, $value, $parameters);

        if(count($validator->errors()->all()) == 0){
                $paramValues = $validator->getData();

                $bookingArr = $paramValues['bookingArr'];
                $arrLength = count($bookingArr);

                for($i=0;$i<$arrLength;$i++) {
                    for($j=$i+1;$j<$arrLength;$j++){

                        $bookingArr[$i]['endDateTime'] = DateTimeUtility::addMinutesToDateTime($bookingArr[$i]['startDateTime'],$bookingArr[$i]['duration']);

                        $bookingArr[$j]['endDateTime'] = DateTimeUtility::addMinutesToDateTime($bookingArr[$j]['startDateTime'],$bookingArr[$j]['duration']);

                        $leftBoundCheck = strtotime($bookingArr[$j]['startDateTime']) <= strtotime($bookingArr[$i]['startDateTime']) && 
                            strtotime($bookingArr[$j]['endDateTime']) <= strtotime($bookingArr[$i]['startDateTime']);

                        $rightBoundCheck = strtotime($bookingArr[$j]['startDateTime']) >= strtotime($bookingArr[$i]['endDateTime']) && 
                            strtotime($bookingArr[$j]['endDateTime']) >= strtotime($bookingArr[$i]['endDateTime']);


                        if(!($leftBoundCheck || $rightBoundCheck))
                        {
                            return false;
                        }
                    }
                }
               
        }

        return true;
    }


    /**
     * function is used to validate if the slot being booked is available or not
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateBookingSlotAvailability($attribute, $value, $parameters, $validator) {
        unset($attribute, $value);
        $bookingArrIndex = $parameters[0];

        if(count($validator->errors()->all()) == 0){
                $paramValues = $validator->getData();
                $beauticianId = $paramValues['beauticianId'];

                $bookingParamValues = $paramValues['bookingArr'][$bookingArrIndex];
                $serviceId = $bookingParamValues['serviceId'];
                $bookingStartDateTime = $bookingParamValues['startDateTime'];

                $beauticianService = BeauticianService::where('service_id',$serviceId)->where('beautician_id',$beauticianId)->first();


                $serviceDuration = $beauticianService->duration;

                $bookingEndDateTime = DateTimeUtility::addMinutesToDateTime($bookingStartDateTime,$serviceDuration);


             $slotAvailability = BeauticianAvailabilitySchedule::
                  where('beautician_availability_schedule.beautician_id','=',$beauticianId)
                ->where('beautician_availability_schedule.start_datetime','<=',"$bookingStartDateTime")
                ->where('beautician_availability_schedule.end_datetime','>=',$bookingEndDateTime)
                ->where('beautician_availability_schedule.is_available','=',1)
                ->leftJoin('customer_bookings',function($join) use ($bookingStartDateTime,$bookingEndDateTime){
                         
                         $join->on('beautician_availability_schedule.beautician_id','=','customer_bookings.beautician_id');

                        $join->where('customer_bookings.start_datetime','<=',$bookingStartDateTime)
                              ->where('customer_bookings.end_datetime','>=',$bookingEndDateTime);

                        $join->whereNull('customer_bookings.deleted_at');

                        $join->whereNotIn('customer_bookings.status',[CustomerBooking::IS_CANCELLED,CustomerBooking::PAYMENT_FAILED]);

                })
                ->whereNull('customer_bookings.id')
                ->select('beautician_availability_schedule.id')->get();


          if(count($slotAvailability) == 0)
          {
            return false;
          }
          else
          {
            return true;
          }

               
        }

        return false;
    }
    
    /**
     * function is used to validate if beautician has added same service before or not
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateUniqueBeauticianService($attribute, $value, $parameters, $validator) {
        unset($attribute, $parameters, $validator);
        $userId = \Auth::user()->id;
        $serviceObj = BeauticianService::where('beautician_id', $userId)->where('service_id', $value)->first();
        
        if(!empty($serviceObj)) {
            return false;
        }
        return true;
    }
    
    /**
     * function is used to validate if rated_by or rated_to user id belongs to the customer booking
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateUserBookingId($attribute, $value, $parameters, $validator) {
        unset($attribute, $value, $parameters);
        
        $userId = \Auth::user()->id;
        $paramValues = $validator->getData();
        $arrRatedToUserId = $paramValues['userId'];

        $bookingId = $paramValues['bookingId'];
        $serviceObj = CustomerBooking::where('customer_bookings.id', $bookingId)
                        ->join('customer_bookings_master as cbm','cbm.id', '=', 'customer_bookings.customer_bookings_master_id')
                        ->where(function($query) use($userId, $arrRatedToUserId) {
                            $query->where(function($subWhere) use($userId, $arrRatedToUserId){
                                $subWhere->where('cbm.customer_id', '=', $userId)
                                        ->where('cbm.beautician_id', '=', $arrRatedToUserId);
                            })
                            ->orWhere(function($subWhere) use($userId, $arrRatedToUserId){
                                $subWhere->where('cbm.customer_id', '=', $arrRatedToUserId)
                                        ->where('cbm.beautician_id', '=', $userId);
                            });
                        })
                        ->where('customer_bookings.end_datetime', '<', DB::raw('now()'))->first();
        
        if(!empty($serviceObj)) {
            return true;
        }
        return false;
    }
    
    /**
     * function is used to validate if rated below 4 point then reason needs to be selected
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateUserRatingPoint($attribute, $value, $parameters, $validator) {
        unset($attribute, $parameters);
        
        $paramValues = $validator->getData();
        $reasonId = (!empty($paramValues['reasonId'])?$paramValues['reasonId']:'');
        
        if($value <= BookingRating::MIN_RATING_FOR_REASON && empty($reasonId)) {
            return false;
        }
        return true;
    }
    
    /**
     * function is used to validate reason id w.r.t to customer or beautician pro
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateReasonId($attribute, $value, $parameters, $validator) {
        unset($attribute,$parameters, $validator);
        $userType = \Auth::user()->user_type;
        if($userType == User::IS_BEAUTICIAN) {
            $type = RatingReason::IS_CUSTOMER_TYPE;
        } else {
            $type = RatingReason::IS_BEAUTICIAN_TYPE;
        }
        $reasonObj = RatingReason::where('id', '=', $value)->where('type', '=', $type)->first();
        if(!empty($reasonObj)) {
            return true;
        }
        return false;
    }
    
    /**
     * function is used to validate reason id w.r.t to customer or beautician pro
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateUserRating($attribute, $value, $parameters, $validator) {
        unset($attribute, $value, $parameters);
        if(count($validator->errors()->all()) == 0){
            $userId = \Auth::user()->id;
            $paramValues = $validator->getData();
            $bookingId = $paramValues['bookingId'];
            $ratedToUserId = $paramValues['userId'];
            $bookingRating = BookingRating::where('customer_booking_id', $bookingId)
                                    ->where('rated_by', $userId)->where('rated_to', $ratedToUserId)->first();
            if(!empty($bookingRating)) {
                return false;
            }
            return true;
        }
    }
    
    /**
     * function is used to validate if number of image upload has exceeded to count
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateFixhibitionImageCount($attribute, $value, $parameters, $validator) {
        unset($attribute, $value, $parameters);
        if(count($validator->errors()->all()) == 0){
            $userId = \Auth::user()->id;
            $fixihibitionCount = BeauticianFixhibition::where('user_id', $userId)->count('id');
            if($fixihibitionCount == BeauticianFixhibition::FIXHIBITION_UPLOAD_LIMIT) {
                return false;
            }
            return true;
        }
    }
    
    /**
     * function is used to validate if beautician kit with same already exist or not
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateKitNameDuplicacy($attribute, $value, $parameters, $validator) {
        unset($attribute,$parameters);
        if(count($validator->errors()->all()) == 0){
            $userId = \Auth::user()->id;
            $kitData = BeauticianKit::where('user_id', $userId)->whereIn('kit_name', $value)->first();
            if(!empty($kitData)) {
                return false;
            }
            return true;
        }
    }
    
    /**
     * function is used to validate if qualification array is empty or not and if the qualification's name already exist for same beautician
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateQualificationSpecialityName($attribute, $value, $parameters, $validator) {
        unset($attribute, $value, $parameters);
        if(count($validator->errors()->all()) == 0){
            $paramValues = $validator->getData();
            $speciality = (isset($paramValues['speciality'])?array_filter($paramValues['speciality']):array());
            $qualification = (isset($paramValues['qualification'])?array_filter($paramValues['qualification']):array());
            if(count($speciality) == 0 && count($qualification) == 0) {
                return false;
            }
            return true;
        }
    }
    
    /**
     * function is used to validate duplicacy of qualification name for same beautician
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateQualificationDuplicacy($attribute, $value, $parameters, $validator) {
        unset($attribute,$parameters);
        if(count($validator->errors()->all()) == 0 && count($value) > 0){
            $userId = \Auth::user()->id;
            $qualificationObj = BeauticianQualification::where('user_id', $userId)
                                                        ->whereIn('qualification', $value)->first();
            if(!empty($qualificationObj)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * function is used to validate duplicacy of speciality name for same beautician
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateSpecialityDuplicacy($attribute, $value, $parameters, $validator) {
        unset($attribute,$parameters);
        if(count($validator->errors()->all()) == 0 && count($value) > 0){
            $userId = \Auth::user()->id;
            $specialityObj = BeauticianSpeciality::where('user_id', $userId)
                                                        ->whereIn('speciality', $value)->first();
            if(!empty($specialityObj)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * function is used to validate booking id and its associated beautician id
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateBookingId($attribute, $value, $parameters, $validator) {
        unset($attribute, $parameters);
        if(count($validator->errors()->all()) == 0 && count($value) > 0){
            $userId = \Auth::user()->id;
            
            $bookingObj = CustomerBooking::where('id', $value)->where('beautician_id', $userId)->first();
            if(empty($bookingObj)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * function is used to validate customer id for booking
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateCustomerIdForBooking($attribute, $value, $parameters, $validator) {
        unset($attribute,$parameters);
        if(count($validator->errors()->all()) == 0 && count($value) > 0){
            $bookingId = $validator->getData(); 
            $bookingId = $bookingId['bookingId'];
            $bookingObj = CustomerBooking::where('id', $bookingId)->where('customer_id', $value)->first();
            if(empty($bookingObj)) {
                return false;
            }
        }
        return true;
    }

    /**
     * function is used to validate if any booking has been made with service id
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param object $validator
     * @return boolean
     */
    public function validateBeauticianServiceIdWithBooking($attribute, $value, $parameters, $validator) {
        unset($attribute,$parameters);
        if(count($validator->errors()->all()) == 0 && count($value) > 0){
            $userId = \Auth::user()->id;
            $timezone = $validator->getData();
            $timezone = $timezone['timezone'];
            $utcOffset = DateTimeUtility::getStandardOffsetUTC($timezone);
            $date = date('Y-m-d H:i:s');
            //check if a booking exist for this service
            $bookingDetail = CustomerBooking::join('beautician_services', function($query) use($value){
                                    $query->on('beautician_services.service_id', '=', 'customer_bookings.service_id')
                                          ->on('beautician_services.id', '=',\DB::raw($value));
                                })
                                ->where(DB::raw('date(convert_tz(customer_bookings.start_datetime, "+00:00", "' . $utcOffset . '"))'), '>=' ,$date)
                                ->where('customer_bookings.beautician_id', $userId)
                                ->where('customer_bookings.status', '!=',CustomerBooking::IS_CANCELLED)->first();
            if(empty($bookingDetail)) {
                return true;
            }
        }
        
        return false;
    }
}