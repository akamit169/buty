<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianService.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (04/04/2018) 
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeauticianService extends \Eloquent {
    use SoftDeletes;

    protected $table = 'beautician_services';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $casts = [
     'cost' => 'float'
    ];
    
    /**
     * function is used to save beautician service
     * @param array $arrData
     * @param int $userId
     * @return type
     */
    public static function saveBeauticianService($arrData, $userId) {
        
        $beauticianServiceModel = new BeauticianService();
        $beauticianServiceModel->beautician_id = $userId;
        $beauticianServiceModel->service_id = $arrData['serviceId'];
        $beauticianServiceModel->parent_service_id = $arrData['parentServiceId'];
        $beauticianServiceModel->duration = $arrData['duration'];
        $beauticianServiceModel->cost = $arrData['cost'];
        $beauticianServiceModel->description = (isset($arrData['description'])?$arrData['description']:'');
        $beauticianServiceModel->tip = (isset($arrData['tip'])?$arrData['tip']:'');
        $beauticianServiceModel->no_of_sessions = (isset($arrData['sessionNumber'])?$arrData['sessionNumber']:'');
        $beauticianServiceModel->time_btw_sessions = (isset($arrData['timeBtwSession'])?$arrData['timeBtwSession']:'');
        
        if(!empty($arrData['discount'])) {
            $beauticianServiceModel->discount = $arrData['discount'];
            $beauticianServiceModel->discount_startdate = $arrData['discountStartDate'];
        }

        if(!empty($arrData['discountedDays'])) {
            $discountEndDay = $arrData['discountedDays'];
            $discountEndDate = date('Y-m-d H:i:s', strtotime($arrData['discountStartDate'].' +'.$discountEndDay.' Days'));
            $discountEndDate = date('Y-m-d H:i:s', strtotime($discountEndDate.' -1 Minutes'));
            $beauticianServiceModel->discount_enddate = $discountEndDate;
            $beauticianServiceModel->discounted_days = $arrData['discountedDays'];
        }
        return $beauticianServiceModel->save();
    }
    
    /**
     * function is used to update beautician service by id
     * @param array $arrData
     * @param int $userId
     * @return type
     */
    public static function updateBeauticianService($arrData) {
        
        $arrServiceData = [];
        $arrServiceData['service_id'] = $arrData['serviceId'];
        $arrServiceData['parent_service_id'] = $arrData['parentServiceId'];
        $arrServiceData['duration'] = $arrData['duration'];
        $arrServiceData['cost'] = $arrData['cost'];
        $arrServiceData['description'] = (isset($arrData['description'])?$arrData['description']:'');
        $arrServiceData['tip'] = (isset($arrData['tip'])?$arrData['tip']:'');
        $arrServiceData['no_of_sessions'] = (isset($arrData['sessionNumber'])?$arrData['sessionNumber']:'');
        $arrServiceData['time_btw_sessions'] = (isset($arrData['timeBtwSession'])?$arrData['timeBtwSession']:'');
        if(!empty($arrData['discount'])) {
            $arrServiceData['discount'] = $arrData['discount'];
        } else {
            $arrServiceData['discount'] = '';
        }
        if(!empty($arrData['discountStartDate'])) {
            $arrServiceData['discount_startdate'] = $arrData['discountStartDate'];
        } else {
            $arrServiceData['discount_startdate'] = null;
        }
        $discountEndDay = (isset($arrData['discountedDays'])?$arrData['discountedDays']:'');
        if($discountEndDay) {
            $discountEndDate = date('Y-m-d H:i:s', strtotime($arrData['discountStartDate'].' +'.$discountEndDay.' Days'));
            $discountEndDate = date('Y-m-d H:i:s', strtotime($discountEndDate.' -1 Minutes'));
            $arrServiceData['discount_enddate'] = $discountEndDate;
            $arrServiceData['discounted_days'] = $arrData['discountedDays'];
        } else {
            $arrServiceData['discount_enddate'] = null;
            $arrServiceData['discounted_days'] = null;
        }
        return BeauticianService::where('id', $arrData['id'])->update($arrServiceData);
    }
    
    /**
     * function is used to get beautician price range
     * @param int/array $beauticianId
     * @return array
     */
    public static function getBeauticianPriceRange($beauticianId) {
        $priceRange = BeauticianService::selectRaw('MIN(cost) as min_cost, MAX(cost) as max_cost, beautician_id');
        if(is_array($beauticianId)) {
            $priceRange->whereIn('beautician_id', $beauticianId);
        } else {
            $priceRange->where('beautician_id', $beauticianId);
        }
        $arrBeauticianPriceRange = $priceRange->groupBy('beautician_id')->get()->toArray();
        return $arrBeauticianPriceRange;
    }
}
