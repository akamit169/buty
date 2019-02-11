<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: FacebookController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit kumar
 * CreatedOn: date (04/08/2018) 
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use InstagramAPI\Instagram;
use Illuminate\Http\Request;

class InstagramController extends Controller {

    const DEBUG = true;
    const TRANCATED_DEBUG = false;
        
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct() {
        //Log out Back
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
    }
  
}
