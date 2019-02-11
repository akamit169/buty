<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: Service.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (13/04/2018) 
 */

namespace App\Models;

class Service extends \Eloquent {
    
    const HAIR_ID = 1;
    const MAKEUP_ID = 2;
    const NAILS_ID = 3;
    const SPRAY_TANNING_ID = 4;
    const WAXING_ID = 5;
    const BROWS_ID = 6;
    const LASHES_ID = 7;
    const COSMETIC_TATTOOING_ID = 8;
    const AESTHETICS_ID = 9;
    const BARBERING_ID = 10;

    protected $table = 'services';
    protected $primaryKey = 'id';


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at'
    ];
    
    /**
     * get list of services and updated description and tips 
     * @param type $beauticianId
     * @return type
     */
    public static function getServiceList($beauticianId) {
        $services = Service::leftJoin('beautician_services',function($leftJoin) use ($beauticianId){
                        $leftJoin->on('beautician_services.service_id','=','services.id')
                                 ->where('beautician_services.beautician_id','=',$beauticianId);
                    })->select('services.*','beautician_services.description as bt_description','beautician_services.tip as bt_tip')->orderBy('services.display_order')->get()->toArray();

        $i=0;
        foreach($services as $service) { 
            
            if(!empty($service['bt_description']))
            {
                $services[$i]['description'] = $service['bt_description'];
            }

            if(!empty($service['bt_tip']))
            {
                $services[$i]['tip'] = $service['bt_tip'];
            }

            unset($services[$i]['bt_description'],$services[$i]['bt_tip']);
            $i++;  
        }
        
        return static::buildTree($services);
        
    }
    
    
    /**
     * function is used to get list of all categories with its child categories
     * @param array $elements
     * @param int $parentId
     * @return array $branch
     */
    public static function buildTree(array $elements, $parentId = 0) {
        $branch = array();  
        
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = static::buildTree($elements, $element['id']);

                if ($children) {
                    $element['children'] = $children;
                }

                $branch[] = $element;
            }
        }

        return $branch;
    }
}
