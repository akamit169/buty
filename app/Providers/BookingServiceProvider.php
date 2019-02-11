<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BookingServiceProvider.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (12/05/2018) 
 */

namespace App\Providers;

use \App\Models\CustomerBooking;
use \App\Models\CustomerBookingMaster;
use \App\Utilities\FileUpload;
use App\Models\BookingRating;
use App\Models\BeauticianService;
use App\Models\BeauticianDetail;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\BookingDispute;
use App\Models\Service;
use DB;
use App\Providers\StripeServiceProvider;
use \App\Utilities\DateTimeUtility;

use \App\Models\Notification;

/**
 * BookingServiceProvider class contains methods for booking management
 */
class BookingServiceProvider extends BaseServiceProvider {

    
    /**
     * function is used to book a service
     * @param array $data
     * @return array
     */
    public static function bookService($data)
    {
        try {

             $user = \Auth::user();

            $bookingArr = $data['bookingArr'];

             foreach ($bookingArr as $value) {
                $serviceIdArr[] = $value['serviceId'];
             }
             
             //checking if another booking is in progress
            if(static::isAnotherBookingInProgress($serviceIdArr,$data['beauticianId']))
            {
                static::$data['message'] = trans('messages.customer_booking.booking_in_progress');
                static::$data['success'] = false;
                return static::$data;
            }
            
            //lock booking services
            static::lockBookingServices($serviceIdArr,$data['beauticianId'],$user->id);
            DB::beginTransaction();

                 $customerBookingMaster = new CustomerBookingMaster();
                 $customerBookingMaster->customer_id = $user->id;
                 $customerBookingMaster->beautician_id = $data['beauticianId'];
                 $customerBookingMaster->booking_address = $data['bookingAddress'];
                 $customerBookingMaster->save();

                 $defaultTravelCost = DB::table('admin_settings')->where('config_key','=','travel_cost')->pluck('config_value')->first();

                 $i=0;
                 foreach ($bookingArr as $bookingInput) {


                     $beauticianService = BeauticianService::where('service_id',$bookingInput['serviceId'])->where('beautician_id',$data['beauticianId'])->first();

                     $booking = new CustomerBooking();
                     $booking->customer_bookings_master_id = $customerBookingMaster->id;
                     $booking->customer_id = $user->id;
                     $booking->beautician_id = $data['beauticianId'];
                     $booking->service_id = $bookingInput['serviceId'];
                     $booking->parent_service_id = $bookingInput['parentServiceId'];
                     $booking->booking_note = $bookingInput['bookingNote'];
                     $booking->start_datetime = $bookingInput['startDateTime'];
                     $booking->end_datetime = DateTimeUtility::addMinutesToDateTime($bookingInput['startDateTime'],$beauticianService->duration);
                     $booking->duration = $beauticianService->duration;
                     $booking->service_cost = $bookingInput['serviceCost'];
                     $booking->discount = $bookingInput['discount'];
                     $booking->actual_cost = $bookingInput['actualCost'];
                     $booking->has_multiple_sessions = $bookingInput['hasMultipleSessions'];
                     $booking->session_no = $bookingInput['sessionNo'];
                     $booking->on_site_service = $bookingInput['onSiteService'];
                     $booking->status =CustomerBooking::IS_DONE_PAYMENT_LEFT;
                     $booking->timezone = $data['timezone'];
                     $booking->utc_offset = $data['utcOffset'];
                     $booking->distance = $data['distance'];
                     $booking->travel_cost = isset($bookingInput['travelCost'])?$bookingInput['travelCost']:0;
                     $booking->default_travel_cost = $defaultTravelCost;
                     $booking->commission_percent = static::getCommissionPercent($data['beauticianId'],$bookingInput['parentServiceId']);
                     $booking->referred_user_id = static::getReferredUserId($user);


                     $naturalImageFolderPath = env('BOOKING_NATURAL_IMAGE_FOLDER');
                     $aspirationImageFolderPath = env('BOOKING_ASPIRATION_IMAGE_FOLDER');

                    if(isset($bookingInput['naturalImage']))
                    {   
                       $fileUpload = new FileUpload();
                       $naturalImageFileName = $fileUpload->uploadFileToS3($bookingInput['naturalImage'],$naturalImageFolderPath);
                      $booking->natural_image = $naturalImageFileName; 
                    }

                    if(isset($bookingInput['aspirationImage']))
                    {
                        $fileUpload = new FileUpload();
                         $aspirationImageFileName = $fileUpload->uploadFileToS3($bookingInput['aspirationImage'],$aspirationImageFolderPath);
                      $booking->aspiration_image = $aspirationImageFileName;
                    }

                    

                     $booking->save();

                     $bookingArr[$i]['id'] = $booking->id;
                     $i++;
                 }




                $beauticianId = $data['beauticianId'];
                $customerId = $user->id;
                $customer = User::join('customer_details',function($join) use($customerId){
                    $join->on('customer_details.user_id','=','users.id')
                         ->where('users.id','=',$customerId);
                })
                ->select('users.*','customer_details.allergies')
                ->first();
                
                $beauticianObj = User::leftJoin('user_devices',function($join) {
                                        $join->on('user_devices.user_id','=','users.id');
                                    })
                                    ->where('users.id',$beauticianId)
                                    ->select('users.*','user_devices.device_token')
                                    ->first();


                //unlock the locked booking services 
                static::unLockBookingServices($serviceIdArr,$data['beauticianId']);


                if(!static::holdBookingPayment($customerBookingMaster->id))
                {
                    DB::rollback();
                    static::unLockBookingServices($serviceIdArr,$data['beauticianId']);
                    static::$data['success'] = false;
                    static::$data['message'] = trans('messages.customer_booking.payment_failed');
                    return static::$data;
                }

                 //notify users after booking has been made successfully
                 static::notifyUsersOnBooking($customer,$beauticianObj,$bookingArr,$data['bookingAddress']);
               

            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();
            static::unLockBookingServices($serviceIdArr,$data['beauticianId']);
            static::setExceptionError($e);
        }
        return static::$data;
    }

     /**
     * function is used to get the currently set commission percent
     * @return type
     */
    public static function getCommissionPercent($beauticianId,$parentServiceId)
    {
      $commissionPercent = 0;
      $commissionPercent = BeauticianService::where('beautician_id',$beauticianId)
                        ->where('parent_service_id',$parentServiceId)
                        ->groupBy('beautician_id')->pluck('premium_commission_percent')->first();

      if($commissionPercent == NULL){$commissionPercent = 0;}
      
      if($commissionPercent == 0)
      {
        $commissionPercent = User::where('id',$beauticianId)->pluck('commission_percent')->first();

      }

      if($commissionPercent == 0)
      {
        $commissionPercent = Service::where('id',$parentServiceId)->pluck('commission_percent')->first();
      }


      return $commissionPercent;

    }

      /**
     * function is used to return referred user's id
     * @return type
     */
    public static function getReferredUserId($user)
    {
      
      $user = User::where('referral_code_used',$user->referral_code)
            ->where('has_referrer_claimed_cashback',0)->orderBy('id')->first();

      if($user)
      {
        $user->has_referrer_claimed_cashback = 1;
        $user->save();

        $referredUserId = $user->id;
      }
      else
      {
        $referredUserId=null;
      }

      return $referredUserId;

    }


     /**
     * function is used to hold booking payment
     * @return type
     */
    public static function holdBookingPaymentCron() {
        try {

            //get all the bookings whose end date is less than or equal to X days and charge not created on stripe
            $pendingPayments = CustomerBooking::join('users','users.id','customer_bookings.customer_id')
                                  ->join('users as beautician','beautician.id','customer_bookings.beautician_id')
                                  ->where('customer_bookings.status','=',CustomerBooking::IS_DONE_PAYMENT_LEFT)
                                  ->where(DB::raw("TIMESTAMPDIFF(SECOND,now(),customer_bookings.end_datetime)/(3600*24)"),'<=',7)
                                  ->select('customer_bookings.actual_cost as cost','customer_bookings.start_datetime','users.stripe_customer_id','beautician.stripe_bank_account_id','customer_bookings.id','customer_bookings.commission_percent')
                                  ->get();




            $failedPaymentsBookingIdArr = [];
            $cancelfailedPaymentsBookingIdArr = [];
            foreach ($pendingPayments as $pendingPayment) {
                $charge = StripeServiceProvider::createCharge($pendingPayment->stripe_customer_id,$pendingPayment->cost,$pendingPayment->stripe_bank_account_id,$pendingPayment->commission_percent);

                if($charge)
                {
                    CustomerBooking::where('customer_bookings.id',$pendingPayment->id)
                                    ->update(
                                        ['status' => CustomerBooking::PAYMENT_HELD,'stripe_charge_id' => $charge->id]
                                        );
                }
                else
                {   
                    //if charge hold failed for bookings starting in less than 2 days, cancel them marking payment failed
                   if(strtotime($pendingPayment->start_datetime) - strtotime(date('Y-m-d H:i:s')) < 48*3600 )
                   {
                     CustomerBooking::where('customer_bookings.id',$pendingPayment->id)
                                      ->update(['status' => CustomerBooking::PAYMENT_FAILED]);

                     array_push($cancelfailedPaymentsBookingIdArr,$pendingPayment->id);
                   }
                   else
                   {
                     array_push($failedPaymentsBookingIdArr,$pendingPayment->id);
                   }
                }
                
            }


            //send failed payments email
            if(count($failedPaymentsBookingIdArr))
            {
                static::sendBookingPaymentHoldFailureEmail($failedPaymentsBookingIdArr);
            }


            //send cancel failed payments email
            if(count($cancelfailedPaymentsBookingIdArr))
            {
             static::sendBookingCancelledDueToPaymentHoldFailureEmail($cancelfailedPaymentsBookingIdArr);
            }

    


        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }


    public static function isAnotherBookingInProgress($serviceIdArr,$beauticianId){

        
         $servicesAvailableCount = BeauticianService::where('beautician_id',$beauticianId)
                                ->whereIn('service_id',$serviceIdArr)
                                ->where('locked_by',0)->count();

        return $servicesAvailableCount?false:true;

    }


    /** mark the services as locked so that other user cannot book the same **/
    public static function lockBookingServices($serviceIdArr,$beauticianId,$customerId){
  
         BeauticianService::where('beautician_id',$beauticianId)
                            ->whereIn('service_id',$serviceIdArr)
                            ->update(['locked_by' => $customerId]);

    }

    /** release the lock on services so that other users can book **/
    public static function unLockBookingServices($serviceIdArr,$beauticianId){
  
         BeauticianService::where('beautician_id',$beauticianId)
                             ->whereIn('service_id',$serviceIdArr)
                             ->update(['locked_by' => 0]);


    }


      /** notify users after a booking is done successfully **/
    public static function notifyUsersOnBooking($customer,$beautician,$bookingArr,$bookingAddress){

                 //send booking email to customer and beauty pro
                static::sendBookingEmailToCustomer($customer,$beautician,$bookingArr,$bookingAddress);
                static::sendBookingEmailToBeautyPro($customer,$beautician,$bookingArr,$bookingAddress);


                $badgeCount =  static::getUserUnreadNotification($beautician->id);

                //send push notifications
                foreach ($bookingArr as $booking) {
                     $message = trans('messages.notification.booking_done',['name' => trim($customer->first_name." ".$customer->last_name), 'serviceName' => $booking['serviceName']]);

                               
                     //prepare data to be inserted into db
                        $ntData = [
                                             'type'=>Notification::BOOKING_DONE, 
                                             'sender_id'=>$customer->id, 
                                             'recipient_id'=>$beautician->id,
                                             'booking_id'=>$booking['id']
                                            ];

                   $notificationId = Notification::insertGetId($ntData);

                    $params = ['data'=>['bookingId'=>$booking['id'], 'notificationType'=>  Notification::BOOKING_DONE,'id' => $notificationId] ];

                    if($beautician->device_token)
                    {
                         $params['badge'] = $badgeCount;
                         NotificationServiceProvider::sendPushIOS($beautician->device_token, $message, $params);

                    }


                  //update new notification count
                  User::where('id', $beautician->id)->update(['new_notifications_count' => DB::raw('new_notifications_count + 1')]);
                }

             

              
                
    }


    /** send booking details on email to customer **/
    public static function sendBookingEmailToCustomer($customer,$beautician,$bookingArr,$bookingAddress){

        $to = $customer->email;
        $subject = trans('messages.email_subject.booking_confirmation_customer');
        \Mail::send('email.booking_confirmation_customer', ['customer'=>$customer, 'beautician' => $beautician,'bookingArr' => $bookingArr,'bookingAddress' => $bookingAddress], function($message) use ($to,$subject) {
                    $message->to($to)->subject($subject);
                    $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                });
    }


     /** send booking details on email to beautician **/
    public static function sendBookingEmailToBeautyPro($customer,$beautician,$bookingArr,$bookingAddress){

        $to = $beautician->email;
        $subject = trans('messages.email_subject.booking_confirmation_beautician');
        \Mail::send('email.booking_confirmation_beautician', ['customer'=>$customer, 'beautician' => $beautician,'bookingArr' => $bookingArr,'bookingAddress' => $bookingAddress], function($message) use ($to,$subject) {
                    $message->to($to)->subject($subject);
                    $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                });
    }

    /**
     * function is used to rate and review an user
     * @param array $arrData
     * @return array
     */
    public static function rateReviewUser($arrData) {
        try {
            $arrRatedData = array(); $status = $belowRatingStatus = false;
            $user = \Auth::user(); 
            $arrRatedData['customer_booking_id'] = $arrData['bookingId'];
            $arrRatedData['rated_by'] = $user->id;
            $arrRatedData['rated_to'] = $arrData['userId'];
            $arrRatedData['rating'] = $arrData['rating'];
            $arrRatedData['comment'] = (!empty($arrData['comment'])?$arrData['comment']:'');
            if($arrData['rating'] <= BookingRating::MIN_RATING_FOR_REASON) {
                $arrRatedData['below_rating_reason'] = (!empty($arrData['reasonId'])?$arrData['reasonId']:'');
                $belowRatingStatus = true;
            }
            
            $status = BookingRating::insert($arrRatedData);
            if($status) {
                if($user->user_type == User::IS_BEAUTICIAN) {
                    $ratedToUserType = 'customer';
                } else {
                    $ratedToUserType = 'beauty Pro';
                }
                if($belowRatingStatus) {
                    //send email to admin
                    $otherUserObj = User::where('id', $arrData['userId'])->first();
                    $mailData = ['currentUserName'=> \Auth::user()->first_name.' '.\Auth::user()->last_name, 
                                    'otherUserName'=> $otherUserObj->first_name.' '.$otherUserObj->last_name,
                                    'rating'=> $arrData['rating'], 'comment'=> $arrData['comment']];
                    if(\Auth::user()->user_type == User::IS_BEAUTICIAN) {
                        $mailData['otherUserType'] = 'Customer';
                        $mailData['currentUserType'] = 'Beauty Pro';
                    } else {
                        $mailData['otherUserType'] = 'Beauty Pro';
                        $mailData['currentUserType'] = 'Customer';
                    }
                    //get Admin url
                    $adminObj = User::where('user_type', User::IS_ADMIN)->first();
                    \Mail::send('email.admin.rate_review_comment', ['mailData'=>$mailData], function($message) use ($adminObj) {
                            $message->to($adminObj->email)->subject('Rate & Review | Comment');
                            $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                    });
                }
                //update user rating and rate count
                $rating = BookingRating::where('rated_to', $arrData['userId'])->selectRaw('SUM(rating)/COUNT(id) as avgRating')->first();
                if(!empty($rating)) {
                    User::where('id', $arrData['userId'])->update(['rating'=>$rating->avgRating, 'review_count'=>DB::raw('review_count+1')]);
                }
                static::$data['message'] = trans('messages.customer_booking.rated_success');
                static::$data['message'] = str_replace('{user}', $ratedToUserType, static::$data['message']);
            } else {
                static::$data['message'] = trans('messages.customer_booking.rated_failure');
            }
            
            static::$data['success'] = $status;

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }
    
    /**
     * function is used to mark a service complete
     * @param array $arrData
     * @return type
     */
    public static function markServiceComplete($arrData) {
        try {
            static::$data['success'] =  false;
            $bookingId = $arrData['bookingId'];
            static::$data['success'] = CustomerBooking::where('id', $bookingId)->update(['status'=>  CustomerBooking::IS_DONE_PAYMENT_LEFT]);
            if(static::$data['success']) {
                static::$data['message'] = trans('messages.customer_booking.service_completed_success');
            } else {
                static::$data['message'] = trans('messages.customer_booking.service_completed_failure');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }
    
     /**
     * function is used to complete customer booked service whose end datetime is over
     * @return type
     */
    public static function completeCustomerBookedService() {
        try {
        
            $pendingPayments = CustomerBooking::join('users','users.id','=','customer_bookings.beautician_id')
                               ->leftjoin('user_devices as bud', 'bud.user_id', '=', 'users.id') 
                               ->leftjoin('user_devices as cud', 'cud.user_id', '=', 'customer_bookings.customer_id')
                               ->where(DB::raw('TIMESTAMPDIFF(HOUR,customer_bookings.end_datetime, now())'), '>',CustomerBooking::PAYMENT_TIME_AFTER_BOOKING)
                               ->where('customer_bookings.status', '=',CustomerBooking::PAYMENT_HELD)
                               ->where('customer_bookings.stripe_charge_id','!=','')
                               ->select('customer_bookings.id','customer_bookings.stripe_charge_id','users.stripe_bank_account_id', 'bud.device_token as beautician_device_token',
                                       'cud.device_token as customer_device_token', 'users.id as beautician_id', 'customer_bookings.customer_id')
                               ->get();

            $adminDetail = User::where('user_type', User::IS_ADMIN)->first();
            foreach ($pendingPayments as $pendingPayment) {
                
                //capture charge  
                $charge = StripeServiceProvider::captureCharge($pendingPayment->stripe_charge_id,$pendingPayment->stripe_bank_account_id);

                if($charge)
                {
                    //mark payment as done
                   CustomerBooking::where('id',$pendingPayment->id)->update(['status' => CustomerBooking::IS_PAYMENT_DONE]);

                    static::sendRatingPushNotification($pendingPayment, $adminDetail);
                }
                else
                {
                   \Log::error("Final Payment failed for booking ID: ".$pendingPayment->id);
                }

                
            }
            
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }


    /**
     * function is used to hold booking payment
     * @return type
     */
    public static function holdBookingPayment($bookingMasterId) {

            //get bookings whose start date is less than or equal to X days and charge not created on stripe
            $pendingPayments = CustomerBooking::join('users','users.id','customer_bookings.customer_id')
                                  ->join('users as beautician','beautician.id','customer_bookings.beautician_id')
                                  ->where('customer_bookings_master_id','=',$bookingMasterId)
                                  ->where(DB::raw("TIMESTAMPDIFF(SECOND,now(),customer_bookings.end_datetime)/(3600*24)"),'<=',7)
                                  ->select('customer_bookings.actual_cost as cost','customer_bookings.start_datetime','users.stripe_customer_id','beautician.stripe_bank_account_id','customer_bookings.id','customer_bookings.commission_percent')
                                  ->get();
            $i=0;
            $failedPaymentsBookingIdArr = [];
            $cancelfailedPaymentsBookingIdArr = [];

            foreach ($pendingPayments as $pendingPayment) {
                $charge = StripeServiceProvider::createCharge($pendingPayment->stripe_customer_id,$pendingPayment->cost,$pendingPayment->stripe_bank_account_id,$pendingPayment->commission_percent);

                if($charge)
                {
                    CustomerBooking::where('customer_bookings.id',$pendingPayment->id)
                                    ->update(['status' => CustomerBooking::PAYMENT_HELD,'stripe_charge_id' => $charge->id]);
                  
                }
                else
                {
                    if($i==0)
                    {
                        return false;
                    }
                    else
                    {
                        
                        //if charge hold failed for bookings starting in less than 2 days, cancel them marking payment failed
                       if(strtotime($pendingPayment->start_datetime) - strtotime(date('Y-m-d H:i:s')) < 48*3600 )
                       {
                         CustomerBooking::where('customer_bookings.id',$pendingPayment->id)
                                         ->update(['status' => CustomerBooking::PAYMENT_FAILED]);

                         array_push($cancelfailedPaymentsBookingIdArr,$pendingPayment->id);
                       }
                       else
                       {
                         array_push($failedPaymentsBookingIdArr,$pendingPayment->id);
                       }
                    }
                }

                $i++;
            }

            //send failed payments email
            if(count($failedPaymentsBookingIdArr))
            {
                sendBookingPaymentHoldFailureEmail($failedPaymentsBookingIdArr);
            }

             //send cancel failed payments email
            if(count($cancelfailedPaymentsBookingIdArr))
            {
             static::sendBookingCancelledDueToPaymentHoldFailureEmail($cancelfailedPaymentsBookingIdArr);
            }



       return true;
    }


    /**
     * function is used to send email to the customer in case payment hold on stripe fails
     * @return type
     */
    public static function sendBookingPaymentHoldFailureEmail($bookingIdArr) {

    
           $bookingDetails = static::getBookingDetailsById($bookingIdArr);
           $subject = trans('messages.email_subject.payment_hold_failed');

           foreach ($bookingDetails as $bookingDetail) {

                $customerEmail = $bookingDetail->email;
                \Mail::send('email.customer.payment_hold_failed', ['bookingDetails'=>$bookingDetail], function($message) use ($customerEmail,$subject) {
                            $message->to($customerEmail)->subject($subject);
                            $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                });

              
           }

    }


    /**
     * function is used to send email to the customer and the beautician in case the booking is cancelled because of payment hold      *failure
     * @return type
     */
    public static function sendBookingCancelledDueToPaymentHoldFailureEmail($bookingIdArr) {

    
           $bookingDetails = static::getBookingDetailsById($bookingIdArr);
           $subject = trans('messages.email_subject.booking_cancelled_payment_failed');

           foreach ($bookingDetails as $bookingDetail) {

                $customerEmail = $bookingDetail->email;
                $status = \Mail::send('email.customer.payment_hold_failed_booking_cancelled', ['bookingDetails'=>$bookingDetail], function($message) use ($customerEmail,$subject) {
                            $message->to($customerEmail)->subject($subject);
                            $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                });


                $beauticianEmail =  $bookingDetail->beauticianEmail;
                $status = \Mail::send('email.beautician.payment_hold_failed_booking_cancelled', ['bookingDetails'=>$bookingDetail], function($message) use ($beauticianEmail,$subject) {
                            $message->to($beauticianEmail)->subject($subject);
                            $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                });
              
           }

    }


    /**
     * function is used to get booking details by Ids
     * @return type
     */
    public static function getBookingDetailsById($bookingIdArr) {
           $userType = \Auth::user()->user_type;
           if($userType == User::IS_BEAUTICIAN)
           {
             $profilePicTable = 'customer';
           }
           else
           {
             $profilePicTable = 'beautician';
           }

           $userProfileUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3');
           $customerBookings = CustomerBooking::join('services','services.id','=','customer_bookings.service_id')
                           ->join('services as parentServices','parentServices.id','=','customer_bookings.parent_service_id')
                           ->join('users as customer','customer.id','=','customer_bookings.customer_id')
                           ->join('users as beautician','beautician.id','=','customer_bookings.beautician_id')
                           ->join('customer_bookings_master','customer_bookings_master.id','=','customer_bookings.customer_bookings_master_id')
                           ->whereIn('customer_bookings.id',$bookingIdArr)
                           ->groupBy('customer_bookings.customer_id')
                           ->groupBy('customer_bookings.beautician_id')
                           ->select('parentServices.name as parentService',
                            'customer_bookings_master.booking_address','services.name as serviceName','customer_bookings.*','customer.first_name as customerFirstName','customer.last_name as customerLastName','customer.email','beautician.first_name as beauticianFirstName','beautician.last_name as beauticianLastName','beautician.email as beauticianEmail','beautician.phone_number as beauticianPhone',
                               'beautician.address as beauticianAddress','beautician.suburb as beauticianSuburb','beautician.state as beauticianState','beautician.country as beauticianCountry','beautician.zipcode as beauticianZipcode',
                            DB::raw('IF('.$profilePicTable.'.profile_pic = "", "", CONCAT("'.$userProfileUrl.'",'.$profilePicTable.'.profile_pic)) as profile_pic'))
                           ->get() ;

         $customerBookingArr = [];

         foreach ($customerBookings as $customerBooking) {
            $customerBookingArr[$customerBooking->customer_id][] = $customerBooking;
         }

         return $customerBookingArr;

    }


     /**
     * function is used to get booking summary 
     * @return type
     */
    public static function getBookingSummary($bookingId) { 

          try
          {
            $naturalImageUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('BOOKING_NATURAL_IMAGE_FOLDER');
            $aspirationImageUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('BOOKING_ASPIRATION_IMAGE_FOLDER');
            $userProfileUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3');
            $query = CustomerBooking::join('users', 'users.id', '=', 'customer_bookings.customer_id')
                                            ->join('services as s1', 's1.id', '=', 'customer_bookings.parent_service_id')
                                            ->join('services as s2', 's2.id', '=', 'customer_bookings.service_id')
                                            ->join('customer_details', 'customer_details.user_id', '=', 'users.id')
                                            ->join('beautician_details','beautician_details.user_id','=','customer_bookings.beautician_id')
                                            ->join('customer_bookings_master','customer_bookings_master.id','=','customer_bookings.customer_bookings_master_id')
                                            ->leftjoin('beautician_services', function($join){
                                               $join->on('beautician_services.service_id', '=', 'customer_bookings.service_id')
                                               ->on('beautician_services.beautician_id', '=', 'customer_bookings.beautician_id');
                                             });

                                            
                                          
                                           $query->where('customer_bookings.id', $bookingId);
                                              

                                            $query->select('customer_bookings.*','users.stripe_customer_id','beautician_details.mobile_services','beautician_details.business_name','customer_bookings_master.booking_address','users.first_name','beautician_services.discount_startdate','beautician_services.discount_enddate','users.last_name', 's1.name as parent_service_name',
                                                    's2.name as service_name', 'users.rating', 'users.review_count',
                                                    DB::raw('IF(beautician_services.id IS NULL, s2.tip, beautician_services.tip) as tip'),
                                                    DB::raw('IF(beautician_services.id IS NULL, s2.description, beautician_services.description) as description'),
                                                    DB::raw('IF(customer_bookings.natural_image = "", "", CONCAT("'.$naturalImageUrl.'",customer_bookings.natural_image)) as natural_image'),
                                                    DB::raw('IF(customer_bookings.aspiration_image = "", "", CONCAT("'.$aspirationImageUrl.'",customer_bookings.aspiration_image)) as aspiration_image'),
                                                    DB::raw('IF(users.profile_pic = "", "", CONCAT("'.$userProfileUrl.'",users.profile_pic)) as profile_pic'),
                                                    DB::raw('IF((customer_bookings.status = '.CustomerBooking::PAYMENT_HELD.') and (TIMESTAMPDIFF(SECOND,customer_bookings.end_datetime,now())/3600) BETWEEN 0 and '.CustomerBooking::PAYMENT_TIME_AFTER_BOOKING.',1,0) as can_raise_dispute'));
                                            

              $beauticianBookingDetails = $query->first()->toArray();



              static::$data['bookingDetails'] = $beauticianBookingDetails;
          }
          catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;

    }

    
    /**
     * function used to get user bookings
     * @return type
     */
    public static function getUserBookings() {
        try {
            $perPage = 10;
            $user = \Auth::user();
            $userType = $user->user_type;
            $userId = $user->id;


            if($userType == User::IS_CUSTOMER)
            {
                $query = CustomerBooking::join('users',function($join) use($userId){
                        $join->on('users.id','=','customer_bookings.beautician_id')
                             ->where('customer_bookings.customer_id',$userId)
                             ->whereNull('customer_bookings.deleted_at');
                });
            }
            else
            {
                $query = CustomerBooking::join('users',function($join) use($userId){
                        $join->on('users.id','=','customer_bookings.customer_id')
                             ->where('customer_bookings.beautician_id',$userId)
                             ->whereNull('customer_bookings.deleted_at');
                });
            }

            $query->join('services','services.id','=','customer_bookings.service_id');

            $query->select('customer_bookings.id','customer_bookings.customer_id','customer_bookings.beautician_id','customer_bookings.status','services.name as serviceName','users.first_name','users.last_name');



            $bookings = $query->paginate($perPage);

            static::$data['bookings'] = $bookings;
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }


     /**
     * function used to raise a dispute against a booking
     * @return type
     */
    public static function raiseDispute($data) {
        try {
           $user = \Auth::user();

           if($user->user_type == User::IS_BEAUTICIAN)
           {
             $raisedTo = $data['customerId'];
             $raisedBy = $data['beauticianId'];
           }
           else
           {
             $raisedBy = $data['customerId'];
             $raisedTo = $data['beauticianId'];
           }

           $dispute = BookingDispute::where('raised_by',$raisedBy)->where('raised_to',$raisedTo)
                           ->where('customer_booking_id',$data['bookingId'])->first();
           if($dispute)
           {
            static::$data['success'] = false;
            static::$data['message'] = trans('messages.customer_booking.dispute_exists');
           }
           else
           {
            BookingDispute::insert(['customer_booking_id' => $data['bookingId'], 'raised_by' => $raisedBy,'raised_to' => $raisedTo,'reason' => $data['reason']]);


            $booking = CustomerBooking::where('id',$data['bookingId'])->first();
            $bookingStatus = $booking->status;
            $cancelledStatus = ($bookingStatus == CustomerBooking::PAYMENT_HELD)?CustomerBooking::IS_DISPUTED_PAYMENT_HELD:CustomerBooking::IS_DISPUTED_PAYMENT_DONE;

            $booking->status = $cancelledStatus;
            $booking->save();

            static::$data['message'] = trans('messages.customer_booking.dispute_raised');

            static::sendDisputeRaisedEmail($user,$raisedTo,$data['reason'],$data['bookingId']);
           }

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }


      /**
     * function used to send dispute email
     * @return type
     */
    public static function sendDisputeRaisedEmail($user,$raisedToId,$reason,$bookingId) {
        
             $raiser = [
                                'name' => trim($user->first_name.' '.$user->last_name), 
                                'userType' => $user->user_type==User::IS_BEAUTICIAN?'Beauty Pro':'Customer',
                                'email' => $user->email
                             ];

        
                 $raisedToObj = User::find($raisedToId);
                 $raisedTo = [
                                'name' => trim($raisedToObj->first_name.' '.$raisedToObj->last_name), 
                                'userType' => $raisedToObj->user_type==User::IS_BEAUTICIAN?'Beauty Pro':'Customer',
                                'email' => $raisedToObj->email
                             ];

                $admin = User::where('user_type','=',User::IS_ADMIN)->first(); 
                $adminEmail = $admin->email;

                $subject = trans('messages.email_subject.booking_dispute');
                \Mail::send('email.admin.dispute_raised', ['reason'=>$reason, 'raiser' => $raiser,'raisedTo' => $raisedTo,'bookingId' => $bookingId], function($message) use ($adminEmail,$subject) {
                            $message->to($adminEmail)->subject($subject);
                            $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                });
    }


    /**
     * function used to cancel booking
     * @param $bookingId
     * @return type
     */
    public static function cancelBooking($bookingId) {
        try {
           $user = \Auth::user();

           $booking = CustomerBooking::where('id',$bookingId)
                           ->whereIn('status',[CustomerBooking::IS_DONE_PAYMENT_LEFT,CustomerBooking::PAYMENT_HELD])
                           ->where('start_datetime','>',DB::raw('now()'))
                           ->first();


           if($booking)
           {

             $stripeCustomerId = $user->stripe_customer_id;
             if($user->user_type == User::IS_BEAUTICIAN)
             {
                if(static::cancelBeauticianBooking($booking,$stripeCustomerId))
                {
                    static::$data['message'] = trans('messages.customer_booking.booking_cancelled');
                }
                else
                {
                    static::$data['message'] = trans('messages.customer_booking.cancellation_charge_failed');
                }
             }
             else
             {
                $stripeAccountId = User::find($booking->beautician_id)->stripe_bank_account_id;
                if(static::cancelCustomerBooking($booking,$stripeCustomerId,$stripeAccountId))
                {
                    static::$data['message'] = trans('messages.customer_booking.booking_cancelled');
                }
                else
                {
                    static::$data['message'] = trans('messages.customer_booking.cancellation_charge_failed');
                }
             }
           }
           else
           {  
             $booking = CustomerBooking::where('id',$bookingId)
                           ->whereIn('status',[CustomerBooking::IS_DONE_PAYMENT_LEFT,CustomerBooking::PAYMENT_HELD])
                           ->first();

             $startTimestamp = strtotime($booking->start_datetime);
             $endTimestamp = strtotime($booking->end_datetime);
             $currentTimestamp = strtotime(date('Y-m-d H:i:s'));

             if($currentTimestamp >= $startTimestamp && $currentTimestamp <= $endTimestamp)
             {
                static::$data['message'] = trans('messages.customer_booking.booking_running');
             }
             else
             {
                static::$data['message'] = trans('messages.customer_booking.invalid_booking_id'); 
             }

             static::$data['success'] = false;
           }

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }



      /**
     * function used to handle booking cancellation by the beautician
     * @param $booking Model
     * @return type
     */
    public static function cancelBeauticianBooking($booking,$stripeCustomerId) {
        $user = \Auth::user();

        $booking->status = CustomerBooking::IS_CANCELLED;
        $booking->cancelled_by = $user->id;
        $booking->save();

        $penalty = false; 

        $cancelledBy = BeauticianDetail::where('user_id',$user->id)->select('business_name')->first()->business_name;
        
        //send email
        static::sendBookingCancellationEmail($booking,User::IS_BEAUTICIAN,$penalty);
        
        //send notification
        $deviceToken = UserDevice::where('user_id',$booking->customer_id)->pluck('device_token')->first();
        static::sendBookingCancelledNotification($booking->id,$user->id,$booking->customer_id,$deviceToken,$cancelledBy);
       

        return true;
    }


     /**
     * function used to handle booking cancellation by the customer
     * @param $booking Model
     * @return type
     */
    public static function cancelCustomerBooking($booking,$stripeCustomerId,$stripeAccountId) {

       $user = \Auth::user();
       $timeDiff = (strtotime($booking->end_datetime) - strtotime("now"))/3600;

       $cancelled = true;
       
       if($timeDiff > CustomerBooking::CANCELLATION_THRESHOLD_TIME)
       {
          $booking->status = CustomerBooking::IS_CANCELLED;
          $booking->cancelled_by = $user->id;
          $booking->save();

          $penalty = false;

       }
       else
       {
          $paymentAmount = $booking->actual_cost * (CustomerBooking::CANCELLATION_CHARGE_PERCENT/100);
          $charge = StripeServiceProvider::createImmediateCaptureCharge($stripeCustomerId,$paymentAmount,$stripeAccountId,$booking->commission_percent);

          if($charge)
          {
             $booking->status = CustomerBooking::IS_CANCELLED;
             $booking->cancelled_by = $user->id;
             $booking->cancellation_stripe_charge_id = $charge->id;
             $booking->save();

            $penalty = true;
          }
          else
          {
            $cancelled = false;
          }

       }

       if($cancelled)
       {    
          $cancelledBy = trim($user->first_name.' '.$user->last_name);

         //send email
         static::sendBookingCancellationEmail($booking,User::IS_CUSTOMER,$penalty);

         //send notification
        $deviceToken = UserDevice::where('user_id',$booking->beautician_id)->pluck('device_token')->first();
        static::sendBookingCancelledNotification($booking->id,$user->id,$booking->beautician_id,$deviceToken,$cancelledBy);
       }

       return $cancelled;
    }


     /**
     * function used to send booking cancellation email
     * @return type
     */
    public static function sendBookingCancellationEmail($booking,$cancelledByUserType,$penalty) {
    
             $userObj = \Auth::user();
             $serviceName = Service::where('id',$booking->service_id)->first()->name;
             if($cancelledByUserType == User::IS_BEAUTICIAN)
             {
                $customer = User::find($booking->customer_id);
                $cancelledWith = [
                                    'userType' => 'Customer',
                                    'name' => trim($customer->first_name.' '.$customer->last_name)
                                 ];

               $cancelledBy = [
                                    'userType' => 'Beauty Pro',
                                    'name' => trim($userObj->first_name.' '.$userObj->last_name)
                                 ];

               $toEmail = $customer->email;

               $cancellationPaymentsInfo = "";
             }
             else
             {
                $beautician = User::find($booking->beautician_id);
                $cancelledWith = [
                                    'userType' => 'Beauty Pro',
                                    'name' => trim($beautician->first_name.' '.$beautician->last_name)
                                 ];

                 $cancelledBy = [
                                    'userType' => 'Customer',
                                    'name' => trim($userObj->first_name.' '.$userObj->last_name)
                                 ];
                                 
                $toEmail = $beautician->email;

                $cancellationPaymentsInfo = $penalty?" and the associated payment has been credited to your account":", no payments were associated with the cancellation";
             }

             
             $userName = trim($userObj->first_name.' '.$userObj->last_name);


             $bookingDateTime =DateTimeUtility::convertDateTimeToTimezone($booking->start_datetime,$booking->timezone,'d/m/Y , g:i A');


            $subject = trans('messages.email_subject.booking_cancelled');

            $bookingId = $booking->id;
            $booking = CustomerBooking::join('services as s',function($join) use($bookingId){
                                        $join->on('s.id','=','customer_bookings.service_id')
                                             ->where('customer_bookings.id',$bookingId);
                                        
                                 })
                                ->join('services as ps',function($join){
                                        $join->on('ps.id','=','customer_bookings.parent_service_id');
                                 })
                                ->join('users as customer',function($join){
                                        $join->on('customer.id','=','customer_bookings.customer_id');
                                })
                                ->join('users as beautician',function($join){
                                        $join->on('beautician.id','=','customer_bookings.beautician_id');
                                })
                                ->join('customer_bookings_master','customer_bookings_master.id','=','customer_bookings.customer_bookings_master_id')
                                ->select('customer_bookings.*','customer_bookings_master.booking_address','s.name as service_name','ps.name as parent_service_name',DB::raw("CONCAT(customer.first_name,' ',customer.last_name) as customer_name"),DB::raw("CONCAT(beautician.first_name,' ',beautician.last_name) as beautician_name"))
                                ->first()->toArray();


            //email to the user with whom the booking has been cancelled
            $status = \Mail::send('email.user.booking_cancelled', ['booking' => $booking,'user' => $cancelledWith['name'],'cancelledWith'=>$cancelledBy, 'serviceName' => $serviceName,'bookingDateTime' => $bookingDateTime, 'penaltyInfo' => $cancellationPaymentsInfo], function($message) use ($toEmail,$subject) {
                        $message->to($toEmail)->subject($subject);
                        $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                    });

            
            //email to self 
            if($penalty)
             {
                $penaltyInfo = " with a penalty of ".CustomerBooking::CANCELLATION_CHARGE_PERCENT."% on the booking amount";
             }
             else
             {
                $penaltyInfo = " with no penalties";
             }

            $selfEmail = $userObj->email;
            $status = \Mail::send('email.user.booking_cancelled', ['booking' => $booking,'user' => $userName,'cancelledWith'=>$cancelledWith, 'serviceName' => $serviceName,'bookingDateTime' => $bookingDateTime, 'penaltyInfo' => $penaltyInfo], function($message) use ($selfEmail,$subject) {
                        $message->to($selfEmail)->subject($subject);
                        $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                    });
     }


       /**
     * function used to send booking cancellation notification
     * @return type
     */
    public static function sendBookingCancelledNotification($bookingId,$senderId,$recipientId,$deviceIdentifier,$cancelledBy) {
       
        $message = trans('messages.notification.booking_cancelled',['name' => $cancelledBy]);
        

        //update new notification count
        User::where('id', $recipientId)->update(['new_notifications_count' => DB::raw('new_notifications_count + 1')]);
        
        //data to insert into db
        $ntData = [
                        'type'=>Notification::BOOKING_CANCELLED, 
                        'sender_id'=>$senderId, 'recipient_id'=>$recipientId,
                        'booking_id'=>$bookingId
                        ];

        $notificationId = Notification::insertGetId($ntData);

         $params = ['data'=>['bookingId'=>$bookingId, 'notificationType'=>  Notification::BOOKING_CANCELLED, 'id' => $notificationId]];

        //send push 
        if($deviceIdentifier)
        {
            $params['badge'] = static::getUserUnreadNotification($recipientId);
            NotificationServiceProvider::sendPushIOS($deviceIdentifier, $message, $params);
        
        }
             
     }
    

    /**
     * function is used to fetch user's unread notification count
     * @param int $userId
     * @return type
     */ 
    public static function getUserUnreadNotification($userId) {
        return Notification::where('recipient_id', $userId)->where('is_read', 0)->count('id');
    }
    
    /**
     * function is used to send pending rating push notification
     * @param object $pendingPayment
     */
    public static function sendRatingPushNotification($pendingPayment, $adminDetail) {
        
        //beauty Pro
        $deviceIdentifier = $pendingPayment->beautician_device_token;
        $message = trans('messages.notification.beautician.rating_pending');
        

        //get user's unread notification's count
        $params['badge'] = static::getUserUnreadNotification($pendingPayment->beautician_id);

        //data to insert into db
        $ntData = ['type'=>Notification::IS_RATING_PENDING, 'sender_id'=>$adminDetail->id, 'recipient_id'=>$pendingPayment->beautician_id,
                    'booking_id'=>$pendingPayment->id];

        $notificationId = Notification::insertGetId($ntData);


        $params = ['data'=>['bookingId'=>$pendingPayment->id, 'notificationType'=>  Notification::IS_RATING_PENDING,'id' => $notificationId]];

        NotificationServiceProvider::sendPushIOS($deviceIdentifier, $message, $params);

        
        //customer
        $deviceIdentifier = $pendingPayment->customer_device_token;
        $message = trans('messages.notification.customer.rating_pending');

        //get user's unread notification's count
        $params['badge'] = static::getUserUnreadNotification($pendingPayment->customer_id);

        //data to insert into db
        $ntData = ['type'=>Notification::IS_RATING_PENDING, 'sender_id'=>$adminDetail->id, 'recipient_id'=>$pendingPayment->customer_id,
                    'booking_id'=>$pendingPayment->id];

         $notificationId = Notification::insertGetId($ntData);
         $params = ['data'=>['bookingId'=>$pendingPayment->id, 'notificationType'=>  Notification::IS_RATING_PENDING,'id' => $notificationId]];

        NotificationServiceProvider::sendPushIOS($deviceIdentifier, $message, $params);

        
    }
    
    /**
     * function is used to fetch logged in user pending feedback
     * @return type
     */
    public static function getUserPendingFeedback() {

        $userProfileUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3');
        static::$data['success'] = false; static::$data['message'] = trans('messages.user.pending_feedback__not_found');
        try {
            $userId = \Auth::user()->id;
            $userRole = \Auth::user()->user_type;
        
           if($userRole == User::IS_BEAUTICIAN)
           {
             $profilePicTable = 'cu';
           }
           else
           {
             $profilePicTable = 'bu';
           }


         $feedbackDetail = CustomerBooking::leftJoin('booking_ratings',function($join) use($userId){
                                            $join->on('booking_ratings.customer_booking_id','=','customer_bookings.id')
                                                 ->where('booking_ratings.rated_by',$userId);
                                    })
                                    ->join('beautician_details','beautician_details.user_id','=','customer_bookings.beautician_id')
                                    ->join('users as bu', 'bu.id', '=', 'customer_bookings.beautician_id')
                                    ->join('users as cu', 'cu.id', '=', 'customer_bookings.customer_id')
                                    ->join('services', 'services.id', '=', 'customer_bookings.service_id')
                                    ->where('customer_bookings.status',CustomerBooking::IS_PAYMENT_DONE)
                                    ->select('customer_bookings.*', DB::raw('CONCAT(bu.first_name," ",bu.last_name) as beautician_name'),'beautician_details.business_name',
                                            DB::raw('CONCAT(cu.first_name," ",cu.last_name) as customer_name'),
                                            'services.name as service_name',DB::raw('IF('.$profilePicTable.'.profile_pic = "", "", CONCAT("'.$userProfileUrl.'",'.$profilePicTable.'.profile_pic)) as profile_pic'))
                                    ->where(DB::raw('TIMESTAMPDIFF(HOUR,customer_bookings.end_datetime, now())'), '>',CustomerBooking::PAYMENT_TIME_AFTER_BOOKING)
                                    ->whereNull('booking_ratings.id');


            if($userRole == User::IS_BEAUTICIAN) {
                $feedbackDetail->where('customer_bookings.beautician_id', $userId);
            } else {
                $feedbackDetail->where('customer_bookings.customer_id', $userId);
            }
            $feedbackDetail = $feedbackDetail->get()->toArray();
            if(count($feedbackDetail)>0) {
                static::$data['data'] = $feedbackDetail;
                static::$data['success'] = true;
                static::$data['message'] = trans('messages.user.pending_feedback_found');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }
    
    /**
     * function is used to get booked service list
     * @return type
     */
    public static function getBookedServiceList($input) {
        
        $customerBookingModel = new CustomerBooking();
        $data = array();
        $search = ''; $bookingStatus = (!empty($input['bookingStatus'])?$input['bookingStatus']:'');
        if (isset($input['search']['value'])) {
            $search = $input['search']['value'];
        }
        if (!$search) {
            $results = $customerBookingModel->getBookedServiceListWeb(array('limit' => $input['length'], 'offset' => $input['start'], 'bookingStatus'=>$bookingStatus));
        } else {
            $results = $customerBookingModel->getBookedServiceListWeb(array('q' => $search, 'limit' => $input['length'], 'offset' => $input['start'], 'bookingStatus'=>$bookingStatus));
        }
        foreach ($results['result'] as $result) {
            switch($result->status) {
                case CustomerBooking::IS_PENDING:
                    $result->status = 'Pending';
                    break;
                case CustomerBooking::PAYMENT_HELD:
                    $result->status = 'Payment Pending';
                    break;
                case CustomerBooking::IS_DONE_PAYMENT_LEFT:
                    $result->status = 'Payment Left';
                    break;
                case CustomerBooking::IS_PAYMENT_DONE:
                    $result->status = 'Payment Done';
                    break;
                case CustomerBooking::IS_CANCELLED:
                    $result->status = 'Cancelled';
                    break;
                case CustomerBooking::IS_DISPUTED_PAYMENT_HELD:
                    $result->status = 'On Dispute';
                    break;
                case CustomerBooking::IS_DISPUTED_PAYMENT_DONE:
                    $result->status = 'Dispute Resolved & Payment Done';
                    break;
                case CustomerBooking::PAYMENT_FAILED:
                    $result->status = 'Payment failed';
                    break;
                case CustomerBooking::DISPUTE_RESOLVED_BY_ADMIN:
                    $result->status = 'Dispute Resolved by Admin';
                    break;
                case CustomerBooking::DISPUTE_REJECTED_BY_ADMIN:
                    $result->status = 'Dispute Rejected by Admin';
                    break;
            }
                    $data[] = array(
                        $result->id,
                        ucwords($result->parent_service_name),
                        ucwords($result->service_name),
                        $result->booking_address,
                        $result->actual_cost + $result->travel_cost,
                        $result->start_datetime,
                        $result->end_datetime,
                        $result->beautician_name,
                        $result->customer_name,
                        $result->status,
                    );
        }

        return array('data' => $data, 'recordsTotal' => $results['count'], "recordsFiltered" => $results['count']);
    }
    

    /**
     * function is used to fetch disputed service list
     * @return type
     */
    public static function getDisputedServiceList($input) { 
        
        $customerBookingModel = new CustomerBooking();
        $data = array();
        $search = '';
        if (isset($input['search']['value'])) {
            $search = $input['search']['value'];
        }
        if (!$search) {
            $results = $customerBookingModel->getDisputedServiceListWeb(array('limit' => $input['length'], 'offset' => $input['start']));
        } else {
            $results = $customerBookingModel->getDisputedServiceListWeb(array('q' => $search, 'limit' => $input['length'], 'offset' => $input['start']));
        }
        foreach ($results['result'] as $result) {
            switch($result->status) {
                case CustomerBooking::IS_PENDING:
                    $result->status = 'Pending';
                    break;
                case CustomerBooking::PAYMENT_HELD:
                    $result->status = 'Payment Pending';
                    break;
                case CustomerBooking::IS_DONE_PAYMENT_LEFT:
                    $result->status = 'Payment Left';
                    break;
                case CustomerBooking::IS_PAYMENT_DONE:
                    $result->status = 'Payment Done';
                    break;
                case CustomerBooking::IS_CANCELLED:
                    $result->status = 'Cancelled';
                    break;
                case CustomerBooking::IS_DISPUTED_PAYMENT_HELD:
                    $result->status = 'On Dispute';
                    break;
                case CustomerBooking::IS_DISPUTED_PAYMENT_DONE:
                    $result->status = 'Dispute Resolved & Payment Done';
                    break;
                case CustomerBooking::PAYMENT_FAILED:
                    $result->status = 'Payment failed';
                    break;
            }
                    $data[] = array(
                        $result->id,
                        ucwords($result->parent_service_name),
                        ucwords($result->service_name),
                        $result->booking_address,
                        $result->actual_cost + $result->travel_cost,
                        $result->start_datetime,
                        $result->end_datetime,
                        $result->beautician_name,
                        $result->customer_name,
                        $result->reason,
                        $result->status,
                    );
        }

        return array('data' => $data, 'recordsTotal' => $results['count'], "recordsFiltered" => $results['count']);
    }



    /**
     * function is used to resolve booking dispute
     * @return type
     */
    public static function adminResolveDispute($input){
        try{

             $bookingId = $input['bookingId'];
             CustomerBooking::where('id',$bookingId)
                        ->update(['status' => CustomerBooking::DISPUTE_RESOLVED_BY_ADMIN]);

             static::$data['message'] = trans('messages.customer_booking.dispute_resolved');

        }
        catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
      
    }

    /**
     * function is used to reject booking dispute
     * @return type
     */
    public static function adminRejectDispute($input){
        try{

             $bookingId = $input['bookingId'];
                 

             $booking = CustomerBooking::join('users as customer','customer_bookings.customer_id','=','customer.id')
                   ->join('users as beautician','customer_bookings.beautician_id','=','beautician.id')
                   ->select('beautician.stripe_bank_account_id','customer.stripe_customer_id','customer_bookings.actual_cost','customer_bookings.travel_cost')->first();

              $amount = $booking->actual_cost + $booking->travel_cost;
              $stripeCustomerId = $booking->stripe_customer_id;
              $stripeAccountId = $booking->stripe_bank_account_id;

                //process the payment 
              $charge = StripeServiceProvider::createImmediateCaptureCharge($stripeCustomerId, $amount, $stripeAccountId,$booking->commission_percent);

             if($charge)
             {

                CustomerBooking::where('id',$bookingId)
                        ->update(['status' => CustomerBooking::DISPUTE_REJECTED_BY_ADMIN]);

                static::$data['message'] = trans('messages.customer_booking.dispute_rejected');
             }
             else
             {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.customer_booking.dispute_rejection_failed');
             }

        }
        catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
      
    }


}