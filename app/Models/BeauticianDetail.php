<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianDetail.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (04/04/2018) 
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class BeauticianDetail extends \Eloquent {

    use SoftDeletes;

    protected $table = 'beautician_details';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    const IS_IOS = 1;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

}
