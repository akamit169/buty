<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: NotificationController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (19/06/2018) 
 */

namespace App\Http\Controllers\Service;

use App\Http\Controllers\BaseController;
use App\Providers\NotificationServiceProvider;
use Illuminate\Http\Request;

use App\Http\Requests\Service\SetBeauticianTimelinessRequest;


class NotificationController extends BaseController {

    /**
     * function is used to set up business profile
     * @return type
     */
    public function getNotificationList() {
        $user = \Auth::user();
        $response = NotificationServiceProvider::getNotificationList($user->id);
        return $this->sendJsonResponse($response);
    }


     /**
     * function is used to mark a notification read
     * @return type
     */
    public function markRead(Request $request) {
        $response = NotificationServiceProvider::markRead($request->input('notificationId'));
        return $this->sendJsonResponse($response);
    }


     /**
     * function is used to delete a notification
     * @return type
     */
    public function deleteNotification(Request $request) {
        $response = NotificationServiceProvider::deleteNotification($request->input('notificationId'));
        return $this->sendJsonResponse($response);
    }

     /**
     * function is used to get unread notifcations count
     * @return type
     */
    public function getCount() {
        $response = NotificationServiceProvider::getCount(\Auth::user()->id);
        return $this->sendJsonResponse($response);
    }

     /**
     * function is used to notify customer that whether beautician is on time or running late
     * @param SetBeauticianTimelinessRequest $request
     * @return type
     */
    public function setBeauticianTimeliness(SetBeauticianTimelinessRequest $request ) {
        $response = NotificationServiceProvider::setBeauticianTimeliness($request->input('bookingId'),$request->input('delay',0));
        return $this->sendJsonResponse($response);
    }



    


}
