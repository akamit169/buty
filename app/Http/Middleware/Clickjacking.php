<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: Clickjacking.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class Clickjacking {

    public function handle(Request $request, \Closure $next) {
        return $next($request)->header('X-Frame-Options', 'DENY');
    }

}
