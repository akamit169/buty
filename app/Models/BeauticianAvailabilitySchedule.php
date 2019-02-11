<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianAvailabilitySchedule.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (04/04/2018) 
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class BeauticianAvailabilitySchedule extends \Eloquent {

    use SoftDeletes;

    protected $table = 'beautician_availability_schedule';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    const IS_NOT_AVAILABLE = 0;
    const IS_AVAILABLE = 1;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function saveBeauticianAvailability($arrData, $userId) {
        $arrAvailabilityData = [];
        $status = false;
        foreach ($arrData as $value) {
            if (!empty($value['id'])) {
                $arr = ['beautician_id' => $userId, 'start_datetime' => $value['startDatetime'],
                    'end_datetime' => $value['endDatetime'], 'is_available' => $value['isAvailable'],
                    'slot' => $value['slot']];
                $status = BeauticianAvailabilitySchedule::where('id', '=', $value['id'])->update($arr);
            } else {
                array_push($arrAvailabilityData, ['beautician_id' => $userId, 'start_datetime' => $value['startDatetime'],
                    'end_datetime' => $value['endDatetime'], 'is_available' => $value['isAvailable'],
                    'slot' => $value['slot']]);
            }
        }
        if (count($arrAvailabilityData) > 0) {
            $status = BeauticianAvailabilitySchedule::insert($arrAvailabilityData);
        }
        return $status;
    }

}
