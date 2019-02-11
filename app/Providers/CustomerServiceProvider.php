<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: CustomerServiceProvider.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (13/03/2018) 
 */

namespace App\Providers;

use App\Models\SkinColor;
use App\Models\SkinType;
use App\Models\HairType;
use App\Models\HairlengthType;
use App\Models\User;
use App\Models\FavouriteBeautician;
use App\Providers\UserServiceProvider;
use DB;
use App\Models\CustomerBooking;

/**
 * CustomerServiceProvider class contains methods for Customer management
 */
class CustomerServiceProvider extends BaseServiceProvider {

    /**
     * get Appearance related data
     *
     * @param type $data
     * @return type
     */
    public static function getAppearanceData() {
        try {
            $skinColors = SkinColor::all();

            foreach ($skinColors as $value) {

                $value->image = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('SKIN_COLORS_S3') . $value->image;
            }

            static::$data['skinTypes'] = SkinType::all();
            static::$data['hairTypes'] = HairType::all();
            static::$data['hairlengthTypes'] = HairlengthType::all();
            static::$data['skinColors'] = $skinColors;
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }
    
    /**
     * search beauticians
     * @param type $data
     * @return type
     */
    public static function searchBeautician($data) {
        try {

            $user = \Auth::user();



            if (isset($data['lat']) && isset($data['lng'])) {
                $lat = $data['lat'];
                $lng = $data['lng'];
            } else {
                $lat = $user->lat;
                $lng = $user->lng;
            }

            $perPage = 20;
            $page = isset($data['page']) ? $data['page'] : 1;

            $start = $perPage * ($page - 1);

            $query = User::join('beautician_details', function($join) {
                        $join->on('beautician_details.user_id', '=', 'users.id');
                    });

            $query->join('beautician_services', function($join) {
                $join->on('beautician_services.beautician_id', '=', 'users.id')
                        ->whereNull('beautician_services.deleted_at');
            });

            //filter by beautician business name
            if (isset($data['businessName'])) {
                $query->where(function ($query) use ($data) {
                    $query->where('business_name', 'like', '%' . $data['businessName'] . '%');
                });
            }

            //filter by parent service ids
            if (isset($data['serviceIds'])) {
                $query->whereIn('beautician_services.parent_service_id', $data['serviceIds']);
            }


            //filter by sub services
            if (isset($data['serviceId'])) {
                $query->where('beautician_services.service_id', $data['serviceId']);
            }

            //filter by cruelty free
            if (isset($data['isCrueltyFree'])) {
                $query->where('beautician_details.cruelty_free_makeup', $data['isCrueltyFree']);
            }

            //filter by availability
            if (isset($data['availableAt'])) {
                $query->join('beautician_availability_schedule', function($join) use ($data) {
                            $join->on('beautician_availability_schedule.beautician_id', '=', 'users.id')
                            ->where('beautician_availability_schedule.start_datetime', '<=', $data['availableAt'])
                            ->where('beautician_availability_schedule.end_datetime', '>=', $data['availableAt'])
                            ->whereNull('beautician_availability_schedule.deleted_at');
                        })
                        ->leftJoin('customer_bookings', function($join) use ($data) {

                            $join->on('beautician_availability_schedule.beautician_id', '=', 'customer_bookings.beautician_id');

                            $join->where('customer_bookings.start_datetime', '<=', $data['availableAt'])
                            ->where('customer_bookings.end_datetime', '>=', $data['availableAt']);
                        })
                        ->whereNull('customer_bookings.id');
            }

            $distance = "6371 * acos( cos( radians('$lat') ) * 
                      cos( radians( users.lat ) ) * 
                      cos( radians( users.lng ) - 
                      radians('$lng') ) + 
                      sin( radians('$lat') ) * 
                      sin( radians( users.lat ) ) )";

           //filter users within 200 km radius
           if(!isset($data['businessName']) || empty($data['businessName']))
           {
              $query->where(DB::raw("ROUND($distance)"),'<=',200);
           }

           //distance filter for mobile only beauty pro with a defined work radius 
           $query->where(function($query) use ($distance){
              $query->where([ ['beautician_details.work_radius','>',0],['users.address','=','""'],['beautician_details.work_radius','>=',DB::raw("ROUND($distance)")] ])
                ->orWhere('users.address','!=','""')
                ->orWhere([ ['beautician_details.work_radius','=',0],['beautician_details.mobile_services','=',1]]);
           });


           $profilePicBasePath = env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3');
           
           $query->select('beautician_services.beautician_id','users.first_name','users.last_name',
            DB::raw("CONCAT('$profilePicBasePath',users.profile_pic) as profile_pic"),'beautician_details.business_name',
            'beautician_details.mobile_services',
            DB::raw("ROUND($distance) as distance"),'users.address','beautician_details.work_radius',
            DB::raw("CASE WHEN CURDATE() BETWEEN discount_startdate and discount_enddate THEN beautician_services.discount ELSE 0 END as discount"),'users.rating',DB::raw('COUNT(*) as serviceCount'),DB::raw('MIN(beautician_services.cost) as minCharge'),DB::raw('MAX(beautician_services.cost) as maxCharge,beautician_services.parent_service_id'))
            ->groupBy('beautician_services.beautician_id');
            
            if(isset($data['sortByCost']))
            {
              $order = $data['sortByCost']==0?'asc':'desc';
              $query->orderBy(DB::raw("MIN(beautician_services.cost)"),$order);
            }
            else if(isset($data['sortByRating']))
            {
              $order = $data['sortByRating']==0?'asc':'desc';
              $query->orderBy('users.rating',$order);
            }
            else
            {
              //sort by distance
               $query->orderBy(DB::raw($distance)); 
            }

            $totalRecords = count($query->get());

            static::$data['perPage'] = $perPage;
            static::$data['total'] = $totalRecords;
            static::$data['beauticians'] = $query->skip($start)->take($perPage)->get();
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * mark beautician as favourite
     *
     * @param type $data
     * @return type
     */
    public static function markBeauticianFavourite($beauticianId) {
        try {
            $user = \Auth::user();
            $favourite = FavouriteBeautician::where('customer_id', $user->id)
                            ->where('beautician_id', '=', $beauticianId)->first();

            if ($favourite) {
                $favourite->delete();
                static::$data['message'] = trans('messages.customer.unfavourite');
            } else {
                FavouriteBeautician::insert(['customer_id' => $user->id, 'beautician_id' => $beauticianId]);
                static::$data['message'] = trans('messages.customer.favourite');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * get Favourite Beauticians
     * @return type
     */
    public static function getFavouriteBeauticians($page) {
        try {
            $perPage = 10;
            $start = $perPage * ($page - 1);

            $user = \Auth::user();
            $customerId = $user->id;

            $lat = $user->lat;
            $lng = $user->lng;

            $distance = "6371 * acos( cos( radians('$lat') ) * 
                      cos( radians( users.lat ) ) * 
                      cos( radians( users.lng ) - 
                      radians('$lng') ) + 
                      sin( radians('$lat') ) * 
                      sin( radians( users.lat ) ) )";


            $query = FavouriteBeautician::join('users', function($join) use($customerId) {
                        $join->on('favourite_beauticians.beautician_id', '=', 'users.id')
                                ->where('favourite_beauticians.customer_id', $customerId);
                    });

            $query->join('beautician_details', function($join) {
                $join->on('beautician_details.user_id', '=', 'users.id');
            });

            $query->join('beautician_services', function($join) {
                $join->on('beautician_services.beautician_id', '=', 'users.id')
                        ->whereNull('beautician_services.deleted_at');
            });

            $profilePicBasePath = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('USER_PROFILE_PIC_S3');

            $query->select('beautician_services.beautician_id', 'users.first_name', 'users.last_name', 'beautician_details.business_name', DB::raw("CONCAT('$profilePicBasePath',users.profile_pic) as profile_pic"), 'beautician_details.work_radius', DB::raw("ROUND($distance) as distance"), 'users.address', DB::raw("CASE WHEN CURDATE() BETWEEN discount_startdate and discount_enddate THEN discount ELSE 0 END as discount"), 'users.rating', DB::raw('COUNT(*) as serviceCount'), DB::raw('MIN(beautician_services.cost) as minCharge'), DB::raw('MAX(beautician_services.cost) as maxCharge,beautician_services.parent_service_id'))
                    ->groupBy('beautician_services.beautician_id')->orderBy('favourite_beauticians.id', 'desc');


            static::$data['perPage'] = $perPage;
            static::$data['total'] = count($query->get());
            static::$data['beauticians'] = $query->skip($start)->take($perPage)->get();
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * get customer details
     * @return type
     */
    public static function getCustomerDetails($customerId) {
        try {

            static::$data['user'] = UserServiceProvider::getCustomerDetails($customerId);
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to get customer's current booking of current date
     * @param array $input
     * @return type
     */
    public static function getCustomerCurrentBooking($input) {
        try {
            $userObj = \Auth::user();
            $startDateTime = $input['startDateTime'];
            $showPastBookings = $input['showPastBookings'];
            static::$data['bookingDetails'] = [];

            static::$data['bookingDetails'] = CustomerBooking::getCurrentBookingDetails($startDateTime, $showPastBookings, $userObj->id);
            if (count(static::$data['bookingDetails']) > 0) {
                static::$data['message'] = trans('messages.customer.booking_available');
            } else {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.customer.no_booking_available');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to delete customer current booking
     * @param array $input
     * @return type
     */
    public static function deleteCustomerCurrentBooking($input) {
        try {
            $userObj = \Auth::user();
            $customerBookingId = $input['customerBookingId'];
            static::$data['success'] = CustomerBooking::where('id', $customerBookingId)->where('customer_id', $userObj->id)->delete();
            if (static::$data['success']) {
                static::$data['message'] = trans('messages.customer.booking_cancelled_success');
            } else {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.customer.booking_cancelled_failure');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

}
