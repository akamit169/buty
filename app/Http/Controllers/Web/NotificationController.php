<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: NotificationController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit Yadav
 * CreatedOn: date (10/07/2018) 
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Providers\NotificationServiceProvider;

class NotificationController extends BaseController {
    
    /**
     * function is used to get notifications view
     */
    public function getNotificationsList() {
        return view('beautician.notifications');
    }


    /**
     * function is used to get the list of notifications
     */
    public function getNotificationsListAjax() {
        $userObj = \Auth::user();
        $response = NotificationServiceProvider::getNotificationList($userObj->id);
        return $this->sendJsonResponse($response);
    }


    /**
     * function is used to get the count of new notifications
     */
    public function getNewNotificationCount() {
        $response = NotificationServiceProvider::getNewNotificationCount();
        return $this->sendJsonResponse($response);
    }


    
    
}
