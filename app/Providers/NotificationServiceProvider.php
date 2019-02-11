<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: NotificationServiceProvider.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (19/06/2018) 
 */

namespace App\Providers;
use App\Models\Notification;
use App\Models\CustomerBooking;
use App\Models\User;
use App\Models\BeauticianAvailabilitySchedule;

use DB;

use App\Providers\BookingServiceProvider;


/**
 * NotificationServiceProvider class contains methods for Notifications management
 */
class NotificationServiceProvider extends BaseServiceProvider {

  
    /**
     * function is used to get notifications list
     * @param int $beauticianId
     * @return array
     */
    public static function getNotificationList($recipientId) {
        try {

            $user = \Auth::user();
            $perPage = 10;

            $profilePicBasePath = env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3');
            $notifications = Notification::where('recipient_id',$recipientId)
                             ->where('type','!=',Notification::IS_RATING_PENDING)
                             ->join('users','users.id','=','notifications.sender_id')
                             ->join('users as recipient','recipient.id','=','notifications.recipient_id')
                             ->leftjoin('customer_bookings','customer_bookings.id','=','notifications.booking_id')
                             ->leftjoin('users as customer','customer.id','=','customer_bookings.customer_id')
                             ->leftjoin('services','customer_bookings.service_id','=','services.id')
                             ->leftjoin('beautician_details','beautician_details.user_id','=','customer_bookings.beautician_id')
                             ->select('notifications.id','notifications.sender_id','notifications.recipient_id','notifications.booking_id','notifications.is_read','notifications.created_at','beautician_details.business_name','notifications.type','users.first_name as senderFirstName','users.last_name as senderLastName','recipient.first_name as recipientFirstName','recipient.last_name as recipientLastName',DB::raw('IF(users.profile_pic = "", "", CONCAT("'.$profilePicBasePath.'",users.profile_pic)) as profile_pic'),'recipient.user_type','services.name as serviceName','customer_bookings.beautician_delay_duration','customer_bookings.customer_id','customer_bookings.start_datetime','customer_bookings.cancelled_by','customer_bookings.end_datetime',DB::raw('CONCAT(customer.first_name," ",customer.last_name) as customerName'))
                             ->orderBy('notifications.id','desc')
                             ->paginate($perPage);


            foreach ($notifications as $notification) {
               switch ($notification->type) {
                   case Notification::ONE_DAY_BEFORE_BOOKING:
                       $message = 'Your next booking is only 1.5 days away!';
                       break;
                    case Notification::BOOKING_CANCELLED:
                      $userType = User::where('id',$notification->cancelled_by)->first()->user_type;
                      if($userType == User::IS_CUSTOMER)
                      {
                        $name = trim($notification->senderFirstName.' '.$notification->senderLastName);
                      }
                      else
                      {
                        $name = $notification->business_name;
                      }
                      $message = trans('messages.notification.booking_cancelled',['name' => $name]);
                      break;
                    case Notification::BOOKING_DONE:
                      $message = trans('messages.notification.booking_done',['name' => trim($notification->customerName),'serviceName' => $notification->serviceName]);
                      break;
                     case Notification::BEAUTICIAN_ONTIME:
                        $message = trans('messages.notification.beautician.ontime',['serviceName' => $notification->serviceName]);
                       break;
                     case Notification::BEAUTICIAN_ONTIME_CONFIRMATION:
                        $message = trans('messages.notification.beautician.ontime_confirmation');
                       break;
                     case Notification::BEAUTICIAN_LATE:
                       $message = trans('messages.notification.beautician.late',['serviceName' => $notification->serviceName, 'delay' => $notification->beautician_delay_duration]);
                       break;
                     case Notification::SET_AVAILABILITY:
                       $message = trans('messages.notification.beautician.set_availability');
                       break;
                   
               }

               $notification['message'] = $message;
            }


            if(count($notifications) == 0)
            {
                static::$data['message'] = trans('messages.notification_not_found');
            }

        
            static::$data['list'] = $notifications;
            static::$data['newNotificationsCount'] = $user->new_notifications_count;

            $user->new_notifications_count = 0;
            $user->save();

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to mark notifications as read
     * @param int $beauticianId
     * @return array
     */
    public static function markRead($notificationId) {
        try {
             Notification::where('id',$notificationId)->update(['is_read' => 1]);

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }


    /**
     * function is used to delete a notification
     * @param int $beauticianId
     * @return array
     */
    public static function deleteNotification($notificationId) {
        try {
             Notification::where('id',$notificationId)->where('recipient_id',\Auth::user()->id)->delete();
             static::$data['message'] = trans('messages.record_deleted');

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

     /**
     * function is used to delete a notification
     * @param int $beauticianId
     * @return array
     */
    public static function deleteOldNotifications() {
  
             Notification::where(DB::raw("DATEDIFF(now(),created_at)"),'>=',7)
                          ->where('is_read',1)
                          ->delete();

       
    }

    

    /**
     * function is used to get unread notifications count
     * @param int $recipientId
     * @return array
     */
    public static function getCount($recipientId) {
        try {
             $count = Notification::where('recipient_id',$recipientId)->where('is_read',0)
                                    ->where('type','!=',Notification::IS_RATING_PENDING)
                                    ->count();
             static::$data['count'] = $count;

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to get new notifications count
     * @return array
     */
    public static function getNewNotificationCount() {
        try {
             
             static::$data['new_count'] = \Auth::user()->new_notifications_count;

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    


     /**
     * function is used to notify customer that whether beautician is on time or running late
     * @param int $bookingId,$delay (delay time if any)
     * @return array
     */
    public static function setBeauticianTimeliness($bookingId,$delay=false) {
        try {
             if($delay)
             {
                static::$data = static::notifyBeauticianDelay($bookingId,$delay);
             }
             else
             {
               static::$data = static::notifyBeauticianIsOnTime($bookingId);
             }
             
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

     /*
     * function is used to notify customer that beautician is on time
     * @param int $bookingId
     * @return boolean
     */
    public static function notifyBeauticianIsOnTime($bookingId){
    

         $booking = CustomerBooking::join('services','services.id','=','customer_bookings.service_id')
                                     ->leftJoin('user_devices','user_devices.user_id','=','customer_bookings.customer_id')
                                     ->join('beautician_details','beautician_details.user_id','=','customer_bookings.beautician_id')
                                      ->where('customer_bookings.id',$bookingId)

                                      ->select('customer_bookings.*','beautician_details.business_name','user_devices.device_token','services.name as serviceName')
                                      ->first();


         if($booking->status == CustomerBooking::IS_CANCELLED)
         {
            static::$data['success'] = false;
            static::$data['message'] = trans('messages.booking_cancelled_msg');
            return static::$data;
         }


         $adminId = User::where('user_type', User::IS_ADMIN)->pluck('id')->first();


          //prepare data to be inserted into db
            $ntData = [
                                 'type'=>Notification::BEAUTICIAN_ONTIME, 
                                 'sender_id'=>$adminId, 'recipient_id'=>$booking->customer_id,
                                 'booking_id'=>$booking->id
                                ];

         $notificationId = Notification::insertGetId($ntData);

         $params = ['data'=>['bookingId'=>$booking->id, 'notificationType'=>  Notification::BEAUTICIAN_ONTIME,'id' => $notificationId,'businessName' => $booking->business_name ]];

         $message = trans('messages.notification.beautician.ontime',['serviceName' => $booking->serviceName]);

        

            if($booking->device_token)
            {
                $params['badge'] = BookingServiceProvider::getUserUnreadNotification($booking->customer_id);
                NotificationServiceProvider::sendPushIOS($booking->device_token, $message, $params);
            
            }
                        
           


           //update new notifications count
            User::where('id', $booking->customer_id)->update(['new_notifications_count' => DB::raw('new_notifications_count + 1')]);

        return static::$data;
            
    }



      /*
     * function is used to notify customer that beautician is running late
     * @param int $bookingId,$delay
     * @return boolean
     */
    public static function notifyBeauticianDelay($bookingId,$delay){
      
         $profilePicBasePath = env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3');
        
         $booking = CustomerBooking::join('services','services.id','=','customer_bookings.service_id')
                                      ->leftJoin('user_devices','user_devices.user_id','=','customer_bookings.customer_id')
                                      ->join('beautician_details','beautician_details.user_id','=','customer_bookings.beautician_id')
                                      ->join('users as beautician','beautician.id','=','customer_bookings.beautician_id')
                                      ->where('customer_bookings.id',$bookingId)
                                      ->select('customer_bookings.*','user_devices.device_token','services.name as serviceName','beautician.first_name','beautician_details.business_name',
                                            DB::raw("CONCAT('$profilePicBasePath',beautician.profile_pic) as profile_pic"))
                                      ->first();

         if($booking->status == CustomerBooking::IS_CANCELLED)
         {
            static::$data['success'] = false;
            static::$data['message'] = trans('messages.booking_cancelled_msg');
            return static::$data;
         }

         $adminId = User::where('user_type', User::IS_ADMIN)->pluck('id')->first();


          //prepare data to be inserted into db
            $ntData = [
                                 'type'=>Notification::BEAUTICIAN_LATE, 
                                 'sender_id'=>$adminId, 'recipient_id'=>$booking->customer_id,
                                 'booking_id'=>$booking->id
                                ];

          $notificationId = Notification::insertGetId($ntData);



         $message = trans('messages.notification.beautician.late',['serviceName' => $booking->serviceName, 'delay' => $delay]);

         $params = ['data'=>[
                            'bookingId'=>$booking->id, 
                            'notificationType'=>  Notification::BEAUTICIAN_LATE,
                            'name' => $booking->first_name,
                            'profilePic' => $booking->profile_pic,
                            'serviceName' => $booking->serviceName,
                            'serviceStartDateTime' => $booking->start_datetime,
                            'serviceEndDateTime' => $booking->end_datetime,
                            'delay' => $delay,
                            'id' => $notificationId,
                            'businessName' => $booking->business_name 
                            ]
                    ];

   

            if($booking->device_token)
            {
                $params['badge'] = BookingServiceProvider::getUserUnreadNotification($booking->customer_id);
                NotificationServiceProvider::sendPushIOS($booking->device_token, $message, $params);
            
            }
            


           //update new notifications count
            User::where('id', $booking->customer_id)->update(['new_notifications_count' => DB::raw('new_notifications_count + 1')]);


            $booking->beautician_delay_duration = $delay;
            $booking->save();

        
        return static::$data;
            
    }

    /**
     * send Push IOS
     * @param type $deviceIdentifier
     * @param type $message
     * @param type $params
     * @return boolean
     */
    public static function sendPushIOS($deviceIdentifier, $message, $params = false) { 

        if (!$deviceIdentifier || strlen($deviceIdentifier) < 22) {
            return;
        }

        if (env('APP_ENV') == 'local') {
            $config = config('push_notification.apple.sandbox');
        } else {
            $config = config('push_notification.apple.production');
        }

    


        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $config['pem_file']);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $config['passphrase']);

        $err = null;
        $errstr = "";

        // Open a connection to the APNS server
        $fp = stream_socket_client(
                $config['url'], $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);


        if (!$fp) {
            return false;
        }



        $body['aps'] = array(
            'alert' => $message,
            'data' => $params['data'],
            'sound' => 'AlertSound.mp3',
            'badge' => $params['badge']
        );


        // Encode the payload as JSON
        $payload = json_encode($body);


        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceIdentifier) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        fwrite($fp, $msg, strlen($msg));


        fclose($fp);
    }

      /**
     * function is used to notify customers and beautician one day before 
     * @return void
     */
    public static function notifyBeforeBooking() {
        
             
             $bookings = CustomerBooking::join('users','users.id','=','customer_bookings.beautician_id')
                                        ->leftjoin('user_devices as bud', 'bud.user_id', '=', 'users.id') 
                                        ->leftjoin('user_devices as cud', 'cud.user_id', '=', 'customer_bookings.customer_id')->where('customer_bookings.status',CustomerBooking::PAYMENT_HELD)
                                         ->where(DB::raw('TIMESTAMPDIFF(HOUR,now(),customer_bookings.start_datetime)'), '<=',CustomerBooking::NOTIFICATION_TIME_BEFORE_BOOKING)
                                         ->where(DB::raw('TIMESTAMPDIFF(HOUR,customer_bookings.created_at,customer_bookings.start_datetime)'), '>',CustomerBooking::NOTIFICATION_TIME_BEFORE_BOOKING)
                                         ->where('customer_bookings.notified_before_booking',0)
                                         ->select('customer_bookings.id', 'bud.device_token as beautician_device_token',
                                       'cud.device_token as customer_device_token', 'users.id as beautician_id', 'customer_bookings.customer_id')
                                         ->get();

    

             if(count($bookings))
             {
                  $adminId = User::where('user_type', User::IS_ADMIN)->pluck('id')->first();
                  $message = trans('messages.notification.one_day_before_booking');


                  foreach ($bookings as $booking) {
                    

                         //send push  to customer
                        $ntData = [
                                             'type'=>Notification::ONE_DAY_BEFORE_BOOKING, 
                                             'sender_id'=>$adminId, 'recipient_id'=>$booking->customer_id,
                                             'booking_id'=>$booking->id
                                            ];

                        $notificationId = Notification::insertGetId($ntData);


               
                        $params = ['data'=>['bookingId'=>$booking->id, 'notificationType'=>  Notification::ONE_DAY_BEFORE_BOOKING,'id' => $notificationId]];


                    
                        if($booking->customer_device_token)
                        {
                            $params['badge'] = BookingServiceProvider::getUserUnreadNotification($booking->customer_id);
                            NotificationServiceProvider::sendPushIOS($booking->customer_device_token, $message, $params);
                        
                        }



                        //send push to beautician

                         $ntData = [
                                             'type'=>Notification::ONE_DAY_BEFORE_BOOKING, 
                                             'sender_id'=>$adminId, 'recipient_id'=>$booking->beautician_id,
                                             'booking_id'=>$booking->id
                                            ];

                         $notificationId = Notification::insertGetId($ntData);

                         $params = ['data'=>['bookingId'=>$booking->id, 'notificationType'=>  Notification::ONE_DAY_BEFORE_BOOKING,'id' => $notificationId]];


                    
                        if($booking->beautician_device_token)
                        {
                            $params['badge'] = BookingServiceProvider::getUserUnreadNotification($booking->beautician_id);
                            NotificationServiceProvider::sendPushIOS($booking->beautician_device_token, $message, $params);
                        
                        }
                        

                       //update new notifications count
                        User::whereIn('id', [$booking->customer_id,$booking->beautician_id])->update(['new_notifications_count' => DB::raw('new_notifications_count + 1')]);

                        //mark notication as sent
                        CustomerBooking::where('id',$booking->id)->update(['notified_before_booking' => 1]);

                }

             }


        
    }


       /**
     * function is used to send notification and ask the beautician if he/she is on time for the booking
     * @return void
     */
    public static function notificationForBeauticianOnTimeConfirmation() { 
        
             $profilePicBasePath = env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3');
             $bookings = CustomerBooking::join('users','users.id','=','customer_bookings.beautician_id')
                                        ->join('users as customer','customer.id','=','customer_bookings.customer_id')
                                        ->join('services','services.id','=','customer_bookings.service_id')
                                        ->leftjoin('user_devices as bud', 'bud.user_id', '=', 'users.id')
                                        ->where(DB::raw('TIMESTAMPDIFF(SECOND,now(),start_datetime)/3600'), '<=',CustomerBooking::BOOKING_CONFIRMATION_TIME)
                                         ->where('customer_bookings.status',CustomerBooking::PAYMENT_HELD)
                                         ->where('customer_bookings.ontime_confirmation_notification',0)
                                         ->where('customer_bookings.on_site_service',1)
                                         ->select('customer_bookings.id', 'bud.device_token', 'users.id as beautician_id', 'customer_bookings.customer_id','customer.first_name',
                                            DB::raw("CONCAT('$profilePicBasePath',customer.profile_pic) as profile_pic"),'customer_bookings.start_datetime','customer_bookings.end_datetime','services.name as serviceName')
                                         ->groupBy('customer_bookings.id')
                                         ->orderBy('customer_bookings.id','desc')
                                         ->get();


             if(count($bookings))
             {
                  $adminId = User::where('user_type', User::IS_ADMIN)->pluck('id')->first();
                  $message = trans('messages.notification.beautician.ontime_confirmation');

                  $bookingIdArr = [];       

                  foreach ($bookings as $booking) {
                        

                        $ntData = [
                                             'type'=>Notification::BEAUTICIAN_ONTIME_CONFIRMATION, 
                                             'sender_id'=>$adminId, 'recipient_id'=>$booking->beautician_id,
                                             'booking_id'=>$booking->id
                                            ];

                        $notificationId = Notification::insertGetId($ntData);
            
                        $params = [
                                    'data'=>[
                                             'bookingId'=>$booking->id, 
                                             'notificationType'=>  Notification::BEAUTICIAN_ONTIME_CONFIRMATION,
                                             'name' => $booking->first_name,
                                             'profilePic' => $booking->profile_pic,
                                             'serviceName' => $booking->serviceName,
                                             'serviceStartDateTime' => $booking->start_datetime,
                                             'serviceEndDateTime' => $booking->end_datetime,
                                             'id' => $notificationId
                                            ]
                                  ];


                        //send push to beautician
                        if($booking->device_token)
                        {
                            $params['badge'] = BookingServiceProvider::getUserUnreadNotification($booking->beautician_id);
                            NotificationServiceProvider::sendPushIOS($booking->device_token, $message, $params);
                        
                        }
                        

                       //update new notifications count
                        User::where('id', $booking->beautician_id)->update(['new_notifications_count' => DB::raw('new_notifications_count + 1')]);


                      array_push($bookingIdArr, $booking->id);


                }


                //mark as ntf sent
                CustomerBooking::whereIn('id',$bookingIdArr)->update(['ontime_confirmation_notification' => 1]);
             }


        
    }


       /**
     * function is used to send notification to the beautician asking to set the schedule for the next week
     * @return void
     */
    public static function notifyBeauticianToSetAvailabilitySchedule() {
        
             
        $schedule = BeauticianAvailabilitySchedule::leftjoin('user_devices','user_devices.user_id','=','beautician_availability_schedule.beautician_id')
                                                    ->where(DB::raw('DAYNAME(now())'),'=','Sunday')
                                                    ->groupBy('beautician_id')
                                                    ->get();



             if(count($schedule))
             {
                  $adminId = User::where('user_type', User::IS_ADMIN)->pluck('id')->first();
                  $message = trans('messages.notification.beautician.set_availability');


                  foreach ($schedule as $sch) {
                
                        $ntData = [
                                             'type'=>Notification::SET_AVAILABILITY, 
                                             'sender_id'=>$adminId, 'recipient_id'=>$sch->beautician_id
                                            ];

                        $notificationId = Notification::insertGetId($ntData);


                        $params = ['data'=>['notificationType'=>  Notification::SET_AVAILABILITY,'id' => $notificationId]];


                        //send push to beautician
                        if($sch->device_token)
                        {
                            $params['badge'] = BookingServiceProvider::getUserUnreadNotification($sch->beautician_id);
                            NotificationServiceProvider::sendPushIOS($sch->device_token, $message, $params);
                        
                        }
                        

                       //update new notifications count
                        User::where('id', $sch->beautician_id)->update(['new_notifications_count' => DB::raw('new_notifications_count + 1')]);

                      

                }


             }


        
    }
    

    
}
