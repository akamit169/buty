<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: RatingReason.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (19/05/2018) 
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

class RatingReason extends \Eloquent {
    use SoftDeletes;

    protected $table = 'rating_reasons';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    const IS_BEAUTICIAN_TYPE = 1;
    const IS_CUSTOMER_TYPE = 2;
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];
}
