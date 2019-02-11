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

class BeauticianPortfolio extends \Eloquent {

    protected $table = 'beautician_portfolios';


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];
    
    /**
     * function is used to get image count uploaded against a particular service id by a beautician
     * @param int $userId
     * @param int $serviceId
     * @return int
     */
    public static function getBeauticianServiceImageCount($userId, $serviceId) {
        
        $imageCount = BeauticianPortfolio::where('user_id', $userId)
                                ->where('service_id', $serviceId)
                                ->select(DB::raw('count(beautician_portfolios.id) as image_count'))
                                ->first();
        if($imageCount) {
            $imageCount = $imageCount->image_count;
        }
        return $imageCount;
    }
}
