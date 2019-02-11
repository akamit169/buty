<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianPortfolio.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (13/04/2018) 
 */

namespace App\Models;

use Illuminate\Support\Facades\DB;

class BeauticianFixhibition extends \Eloquent {

    protected $table = 'beautician_fixhibition';

    const FIXHIBITION_UPLOAD_LIMIT = 50;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
