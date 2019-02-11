<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: CustomerBookingsMaster.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (19/05/2018) 
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;
use DB;
use \App\Models\CustomerBooking;

class CustomerBookingsMaster extends \Eloquent {
    use SoftDeletes;

    protected $table = 'customer_bookings_master';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    const IS_STATUS_PENDING = 0;
    const IS_STATUS_COMPLETED = 1;
    const IS_STATUS_CANCELLED = 2;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];
    
    /**
     * function is used to get current/future booking details based on dates
     * @param string $currentDt
     * @param int $userId
     * @return array $customerBookingDetails
     */
    public static function getCurrentBookingDetails($currentDt, $userId) {
        $customerBookingDetails = [];
        try {
            $startDt = $currentDt.' 00:00:00';
            $endDt = $currentDt.' 23:59:59';
            $naturalImageUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('BOOKING_NATURAL_IMAGE_FOLDER');
            $aspirationImageUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('BOOKING_ASPIRATION_IMAGE_FOLDER');
            $customerBookingDetails = CustomerBookingsMaster::
                                            join('customer_bookings as cb', function($query) use($startDt, $endDt){
                                                $query->on('cb.customer_bookings_master_id', '=', 'customer_bookings_master.id')
                                                      ->where('cb.start_datetime', '>=', $startDt)
                                                      ->where('cb.end_datetime', '<=', $endDt);
                                            })
                                            ->where('cb.status', CustomerBooking::IS_PENDING)
                                            ->where('customer_bookings_master.status', static::IS_STATUS_PENDING)
                                            ->where('customer_bookings_master.customer_id', $userId)
                                            ->select('customer_bookings_master.id', 'customer_bookings_master.customer_id', 'customer_bookings_master.beautician_id',
                                                    'customer_bookings_master.travel_cost', 'customer_bookings_master.cost as total_cost','customer_bookings_master.booking_address',
                                                    'cb.service_id', 'cb.parent_service_id', 'cb.booking_note', 'cb.booking_address', 'cb.start_datetime', 'cb.end_datetime', 'cb.duration', 'cb.customer_bookings_master_id',
                                                    'cb.service_cost', 'cb.discount', 'cb.actual_cost', 'cb.has_multiple_sessions', 'cb.session_no', 'cb.on_site_service', 'cb.default_travel_cost',
                                                    DB::raw('IF(cb.natural_image = "", "", CONCAT("'.$naturalImageUrl.'",cb.natural_image)) as natural_image'),
                                                    DB::raw('IF(cb.aspiration_image = "", "", CONCAT("'.$aspirationImageUrl.'",cb.aspiration_image)) as aspiration_image'))
                                            ->get()->toArray();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return $customerBookingDetails;
    }
}
