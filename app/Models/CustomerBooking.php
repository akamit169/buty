<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianAvailabilitySchedule.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (04/04/2018) 
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Log;
use \App\Models\CustomerBooking;

class CustomerBooking extends \Eloquent {
    use SoftDeletes;

    protected $table = 'customer_bookings';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    protected $hidden = [
      'updated_at','deleted_at', 'created_at'
    ];

    
    const IS_PENDING = 0;
    const IS_DONE_PAYMENT_LEFT = 1;
    const PAYMENT_HELD = 2;
    const IS_PAYMENT_DONE = 3;
    const IS_CANCELLED = 4;
    const IS_DISPUTED_PAYMENT_HELD = 5;
    const IS_DISPUTED_PAYMENT_DONE = 6;
    const PAYMENT_FAILED = 7;

    const DISPUTE_RESOLVED_BY_ADMIN = 8;
    const DISPUTE_REJECTED_BY_ADMIN = 9;

    const CANCELLATION_THRESHOLD_TIME=24; //24 hours
    const CANCELLATION_CHARGE_PERCENT = 50; //50%

    const PAYMENT_TIME_AFTER_BOOKING=24; //24 Hours
    const NOTIFICATION_TIME_BEFORE_BOOKING=36; //36 Hours
    const BOOKING_CONFIRMATION_TIME=1; //1 Hour

    const ADMIN_SHARE_PERCENT=10; // percentage of the total amount that will go to the admin on payment 


     protected $casts = [
     'service_cost' => 'float',
     'actual_cost' => 'float',
     'default_travel_cost' => 'float',
     'travel_cost' => 'float'
    ];
    
    /**
     * function is used to get current/future booking details based on dates
     * @param string $startDateTime
     * @param string $endDateTime
     * @param int $userId
     * @return array $customerBookingDetails
     */
    public static function getCurrentBookingDetails($startDateTime, $showPastBookings, $userId) { 
        $customerBookingDetails = [];
        try {
            $naturalImageUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('BOOKING_NATURAL_IMAGE_FOLDER');
            $aspirationImageUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('BOOKING_ASPIRATION_IMAGE_FOLDER');
            $userProfileUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3');
            $query = CustomerBooking::join('users', 'users.id', '=', 'customer_bookings.beautician_id')
                                            ->join('services as s1', 's1.id', '=', 'customer_bookings.parent_service_id')
                                            ->join('services as s2', 's2.id', '=', 'customer_bookings.service_id')
                                            ->join('beautician_details', 'beautician_details.user_id', '=', 'users.id')
                                           ->join('customer_bookings_master','customer_bookings_master.id','=','customer_bookings.customer_bookings_master_id')
                                           ->leftjoin('beautician_services', function($join){
                                               $join->on('beautician_services.service_id', '=', 'customer_bookings.service_id')
                                               ->on('beautician_services.beautician_id', '=', 'customer_bookings.beautician_id');
                                             });

                                            $query->where('customer_bookings.customer_id', $userId);


                                            if($showPastBookings == 0)
                                            {
                                              $query->where('customer_bookings.end_datetime', '>=', $startDateTime);
                                            }
                                            else
                                            {
                                              $query->where('customer_bookings.end_datetime', '<=', $startDateTime);
                                            }
                                         
                                           

                                            $query->select('customer_bookings.*','customer_bookings_master.booking_address','beautician_services.discount_startdate','beautician_services.discount_enddate', 'users.first_name', 'users.last_name','users.address as beauticianAddress','users.suburb as beauticianSuburb','users.state as beauticianState','users.country as beauticianCountry','users.zipcode as beauticianZipcode', 's1.name as parent_service_name', 'users.id as beautician_id',
                                                    's2.name as service_name', 'users.rating', 'users.review_count', 'beautician_details.work_radius','beautician_details.business_name',
                                                    DB::raw('IF(beautician_services.id IS NULL, s2.tip, beautician_services.tip) as tip'),
                                                    DB::raw('IF(beautician_services.id IS NULL, s2.description, beautician_services.description) as description'),
                                                    DB::raw('IF(customer_bookings.natural_image = "", "", CONCAT("'.$naturalImageUrl.'",customer_bookings.natural_image)) as natural_image'),
                                                    DB::raw('IF(customer_bookings.aspiration_image = "", "", CONCAT("'.$aspirationImageUrl.'",customer_bookings.aspiration_image)) as aspiration_image'),
                                                    DB::raw('IF(users.profile_pic = "", "", CONCAT("'.$userProfileUrl.'",users.profile_pic)) as profile_pic'),
                                                    DB::raw('IF((customer_bookings.status = '.CustomerBooking::PAYMENT_HELD.') and (TIMESTAMPDIFF(SECOND,customer_bookings.end_datetime,now())/3600) BETWEEN 0 and '.CustomerBooking::PAYMENT_TIME_AFTER_BOOKING.',1,0) as can_raise_dispute'))
                                            ->groupBy('customer_bookings.id')
                                            ->orderBy('customer_bookings.start_datetime', 'desc')
                                            ->orderBy('customer_bookings.id', 'desc');


                                            

              $customerBookingDetails = $query->get()->toArray();

              $customerBookingDetails = static::resolveSameDateBookings($customerBookingDetails);


        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return $customerBookingDetails;
    }


    /**
     * function is used to get current/future booking details of the beautician based on dates
     * @param string $startDateTime
     * @param string $endDateTime
     * @param int $userId
     * @return array $beauticianBookingDetails
     */
    public static function getBeauticianBookingDetails($startDateTime, $showPastBookings, $userId) { 
        $beauticianBookingDetails = [];
        

        try {

            $naturalImageUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('BOOKING_NATURAL_IMAGE_FOLDER');
            $aspirationImageUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('BOOKING_ASPIRATION_IMAGE_FOLDER');
            $userProfileUrl = env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3');
            $query = CustomerBooking::join('users', 'users.id', '=', 'customer_bookings.customer_id')
                                            ->join('services as s1', 's1.id', '=', 'customer_bookings.parent_service_id')
                                            ->join('services as s2', 's2.id', '=', 'customer_bookings.service_id')
                                            ->join('customer_details', 'customer_details.user_id', '=', 'users.id')
                                            ->join('customer_bookings_master','customer_bookings_master.id','=','customer_bookings.customer_bookings_master_id')
                                            ->leftjoin('beautician_services', function($join){
                                               $join->on('beautician_services.service_id', '=', 'customer_bookings.service_id')
                                               ->on('beautician_services.beautician_id', '=', 'customer_bookings.beautician_id');
                                             })
                                            ->leftjoin('booking_ratings',function($join){
                                              $join->on('booking_ratings.customer_booking_id','=','customer_bookings.id')
                                                ->on('rated_by','=','customer_bookings.beautician_id');
                                            });

                                    
                                           
                                            if($showPastBookings == 0)
                                            {
                                             $query->where('customer_bookings.end_datetime', '>=', $startDateTime);
                                            }
                                            else
                                            {
                                              $query->where('customer_bookings.end_datetime', '<', $startDateTime);
                                            }
                                            
                                          
                                           $query->where('customer_bookings.beautician_id', $userId);
                                              
                                            $query->select('customer_bookings.*','users.stripe_customer_id','customer_bookings_master.booking_address','users.first_name','beautician_services.discount_startdate','beautician_services.discount_enddate','users.last_name', 's1.name as parent_service_name',
                                                    's2.name as service_name', 'users.rating', 'users.review_count',
                                                    DB::raw('IF(beautician_services.id IS NULL, s2.tip, beautician_services.tip) as tip'),
                                                    DB::raw('IF(beautician_services.id IS NULL, s2.description, beautician_services.description) as description'),
                                                    DB::raw('IF(customer_bookings.natural_image = "", "", CONCAT("'.$naturalImageUrl.'",customer_bookings.natural_image)) as natural_image'),
                                                    DB::raw('IF(customer_bookings.aspiration_image = "", "", CONCAT("'.$aspirationImageUrl.'",customer_bookings.aspiration_image)) as aspiration_image'),
                                                    DB::raw('IF(users.profile_pic = "", "", CONCAT("'.$userProfileUrl.'",users.profile_pic)) as profile_pic'),
                                                    DB::raw('IF((customer_bookings.status = '.CustomerBooking::PAYMENT_HELD.') and (TIMESTAMPDIFF(SECOND,customer_bookings.end_datetime,now())/3600) BETWEEN 0 and '.CustomerBooking::PAYMENT_TIME_AFTER_BOOKING.',1,0) as can_raise_dispute'),'booking_ratings.id as booking_rating_id')
                                            ->groupBy('customer_bookings.id')
                                            ->orderBy('customer_bookings.start_datetime', 'desc')
                                            ->orderBy('customer_bookings.id', 'desc');
                                            
              $beauticianBookingDetails = $query->get()->toArray();

              $beauticianBookingDetails = static::resolveSameDateBookings($beauticianBookingDetails);

            
             
    

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return $beauticianBookingDetails;
    }

     /**
     * function is used to resolve bookings with the same beautician and having the same start date time
     * In this case the latest booking for the same beautician and having the same start date time is taken into the array
     * @param array $bookingDetailsArr
     * @return bookingsFinalArr
     */
    public static function resolveSameDateBookings($bookingDetailsArr)
    {
      $bookingArr = [];
      $bookingsFinalArr = [];
      $k=0;
      for($i=0;$i<count($bookingDetailsArr);$i++)
      {
        $val = $bookingDetailsArr[$i];
        $assignInArr = false;
        if(!isset($bookingArr[$bookingDetailsArr[$i]['beautician_id']]))
        {
          $assignInArr = true;
          $bookingArr[$val['beautician_id']] = [$val['start_datetime']];
        }
        else
        {
          $inArr = in_array($val['start_datetime'],$bookingArr[$val['beautician_id']]);
          if(!$inArr)
          {
            $assignInArr = true;
            array_push($bookingArr[$val['beautician_id']], $val['start_datetime']);
          }
        }

        if($assignInArr)
        {
          $bookingsFinalArr[$k] = $val;
          $k++;
        }
      }

      return $bookingsFinalArr;

    }
    
    /**
     * function is used to get booked service list
     * @param array $data
     * @return type
     */
    public function getBookedServiceListWeb($data=array()) {
        $resultCount = 0;
        
        $totalResult = DB::table($this->table)
                            ->join('customer_bookings_master', 'customer_bookings_master.id', '=', $this->table.'.customer_bookings_master_id')
                            ->join('users as bu', 'bu.id', '=', $this->table.'.beautician_id')
                            ->join('users as cu', 'cu.id', '=', $this->table.'.customer_id')
                            ->join('services as ps', 'ps.id', '=', $this->table.'.parent_service_id')
                            ->join('services as cs', 'cs.id', '=', $this->table.'.service_id')
                            ->select($this->table.'.*', 'customer_bookings_master.booking_address',DB::raw('CONCAT_WS(" ", bu.first_name, bu.last_name) as beautician_name'),
                                    DB::raw('CONCAT_WS(" ", cu.first_name, cu.last_name) as customer_name'), 'ps.name as parent_service_name',
                                    'cs.name as service_name');
        $searchQuery = '';
        if (isset($data['q'])) {
            $searchQuery = $data['q'];
        }
        if ($data) {
            if (isset($searchQuery) && !empty($searchQuery)) {
                $totalResult->where(function($query) use($searchQuery) {
                        $query->orWhere('ps.name', 'like', '%' . $searchQuery . '%');
                        $query->orWhere('cs.name', 'like', '%' . $searchQuery . '%');
                        $query->orWhere(DB::raw('CONCAT_WS(" ", bu.first_name, bu.last_name)'), 'like', '%' . $searchQuery . '%');
                        $query->orWhere(DB::raw('CONCAT_WS(" ", cu.first_name, cu.last_name)'), 'like', '%' . $searchQuery . '%');
                    });
            }
            if(!empty($data['bookingStatus'])){
                $totalResult->where($this->table.'.status', $data['bookingStatus']);
            }
            $resultCount = $totalResult->get();
            $totalResult->orderby($this->table.'.id', 'desc');
            $result = $totalResult->skip($data['offset'])->take($data['limit'])->get();
        } else {
            $result = $totalResult->get();
            $resultCount = $result;
        }
        $resultCount = count($resultCount);
        return array('count' => $resultCount, 'result' => $result);
    }
    
    /**
     * function is used to get user pending feedback
     */
    public static function getBeauticianPendingFeedback() {
        $userId = \Auth::user()->id;
        $feedbackDetail = $feedbackDetail = CustomerBooking::leftJoin('booking_ratings',function($join) use($userId){
                                            $join->on('booking_ratings.customer_booking_id','=','customer_bookings.id')
                                                 ->where('booking_ratings.rated_by',$userId);
                                    })
                                    ->select('customer_bookings.*')
                                    ->where(DB::raw('TIMESTAMPDIFF(HOUR,customer_bookings.end_datetime, now())'), '>',  CustomerBooking::PAYMENT_TIME_AFTER_BOOKING)
                                    ->whereNull('booking_ratings.id')
                                    ->where('customer_bookings.beautician_id', $userId)
                                    ->whereIn('customer_bookings.status', [CustomerBooking::IS_DONE_PAYMENT_LEFT, 
                                            \App\Models\CustomerBooking::IS_PAYMENT_DONE])->first();
        return  $feedbackDetail;
    }
    
    /**
     * function is used to get disputed booked service list
     * @param array $data
     * @return type
     */
    public function getDisputedServiceListWeb($data=array()) {
        $resultCount = 0;
        
        $totalResult = DB::table($this->table)
                            ->join('customer_bookings_master', 'customer_bookings_master.id', '=', $this->table.'.customer_bookings_master_id')
                            ->join('users as bu', 'bu.id', '=', $this->table.'.beautician_id')
                            ->join('users as cu', 'cu.id', '=', $this->table.'.customer_id')
                            ->join('services as ps', 'ps.id', '=', $this->table.'.parent_service_id')
                            ->join('services as cs', 'cs.id', '=', $this->table.'.service_id')
                            ->join('booking_disputes', 'booking_disputes.customer_booking_id', '=', $this->table.'.id')
                            ->where($this->table.'.status', CustomerBooking::IS_DISPUTED_PAYMENT_HELD)
                            ->select($this->table.'.*', 'customer_bookings_master.booking_address',DB::raw('CONCAT_WS(" ", bu.first_name, bu.last_name) as beautician_name'),
                                    DB::raw('CONCAT_WS(" ", cu.first_name, cu.last_name) as customer_name'), 'ps.name as parent_service_name',
                                    'cs.name as service_name','booking_disputes.reason');


        $searchQuery = '';
        if (isset($data['q'])) {
            $searchQuery = $data['q'];
        }
        if ($data) {
            if (isset($searchQuery) && !empty($searchQuery)) {
                $totalResult->where(function($query) use($searchQuery) {
                        $query->orWhere('ps.name', 'like', '%' . $searchQuery . '%');
                        $query->orWhere('cs.name', 'like', '%' . $searchQuery . '%');
                        $query->orWhere(DB::raw('CONCAT_WS(" ", bu.first_name, bu.last_name)'), 'like', '%' . $searchQuery . '%');
                        $query->orWhere(DB::raw('CONCAT_WS(" ", cu.first_name, cu.last_name)'), 'like', '%' . $searchQuery . '%');
                    });
            }
            $resultCount = $totalResult->get();      
            $totalResult->orderby($this->table.'.id', 'desc');  
            $result = $totalResult->skip($data['offset'])->take($data['limit'])->get(); 
        } else {
            $result = $totalResult->get();
            $resultCount = $result;
        }

        //dd($result);
        $resultCount = count($resultCount);
        return array('count' => $resultCount, 'result' => $result);
    }
}
