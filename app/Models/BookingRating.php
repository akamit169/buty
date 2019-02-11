<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BookingRating.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (19/05/2018) 
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingRating extends \Eloquent {
    use SoftDeletes;

    protected $table = 'booking_ratings';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    const MIN_RATING_FOR_REASON = 3;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];
}
