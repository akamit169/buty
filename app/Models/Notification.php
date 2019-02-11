<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: Notification.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (19/06/2018) 
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends \Eloquent {
    use SoftDeletes;

    const ONE_DAY_BEFORE_BOOKING = 1;
    const IS_RATING_PENDING = 2;
    const BOOKING_CANCELLED = 3;
    const BOOKING_DONE = 4;
    const BEAUTICIAN_ONTIME_CONFIRMATION = 5;
    const BEAUTICIAN_ONTIME = 6;
    const BEAUTICIAN_LATE = 7;
    const SET_AVAILABILITY = 8;
    
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    protected $hidden = [
      'updated_at','deleted_at'
    ];  

}
