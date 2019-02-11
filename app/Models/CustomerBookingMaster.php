<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: CustomerBookingMaster.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (22/05/2018) 
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerBookingMaster extends \Eloquent {
    use SoftDeletes;

    protected $table = 'customer_bookings_master';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    const IS_DONE_PAYMENT_LEFT = 1;
    const IS_PAYMENT_DONE = 2;
    const IS_CANCELLED = 3;
    
}
