<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: CustomerDetail.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (13/04/2018) 
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDetail extends \Eloquent {
    use SoftDeletes;

    protected $dates = ['deleted_at'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];
}
