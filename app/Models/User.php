<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: User.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class User extends \Eloquent {

    use SoftDeletes;

    protected $table = 'users';
    protected static $currentTable = 'users';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    const IS_ADMIN = 1;
    const IS_BEAUTICIAN = 2;
    const IS_CUSTOMER = 3;
    const IS_APPROVAL_PENDING = 0;
    const IS_APPROVED = 1;
    const IS_DISAPPROVED = 2;
    const IS_ACTIVE = 1;
    const IS_INACTIVE = 0;
    const IS_FLAGGED = 1;
    const IS_NOT_FLAGGED = 0;
    const IS_CRUELTY_FREE = 0;
    const IS_NOT_CRUELTY_FREE = 1;

    const GENDER = ['MALE' => 1, 'FEMALE' => 2, 'OTHER' => 3];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'updated_at','deleted_at', 'created_at'
    ];
    
    /**
     * function is used to get beautician list
     * @param array $data
     * @return type
     */
    public function getBeauticianListWeb($data=array()) {
        $resultCount = 0;
        
        $totalResult = DB::table($this->table)
                            ->where($this->table.'.status', static::IS_ACTIVE)
                            ->select($this->table.'.*');
        $searchQuery = '';
        if (isset($data['q'])) {
            $searchQuery = $data['q'];
        }
       
        if(isset($data['approvedStatus'])) {
            $totalResult->where($this->table.'.admin_approval_status', $data['approvedStatus']);
        }
        $totalResult->where($this->table.'.user_type', User::IS_BEAUTICIAN);
        if ($data) {
            if (isset($searchQuery) && !empty($searchQuery)) {
                $totalResult->where(function($query) use($searchQuery) {
                        $query->orWhere($this->table.'.email', 'like', '%' . $searchQuery . '%');
                        $query->orWhere($this->table.'.first_name', 'like', '%' . $searchQuery . '%');
                    });
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
     * function is used to fetch beautician detail
     * @param int $userId
     * @return Object
     */
    public static function fetchBeauticianDetail($userId) {
        
        $userObj = User::join('beautician_details', 'users.id', '=', 'beautician_details.user_id')
                        ->where('users.id', $userId)
                        ->select('users.*', 'beautician_details.abn', 'beautician_details.business_name', 'beautician_details.instagram_link',
                                'beautician_details.police_check_certificate', 'beautician_details.business_description',
                                'beautician_details.cruelty_free_makeup')
                        ->first();
        return $userObj;
    }
    
    /**
     * function is used to approve beautician
     * @param int $userId
     * @return boolean $status | True in case of update else False
     */
    public static function approveBeautician($userId) {
        
        $status = User::where('id', $userId)->update(['admin_approval_status'=>  User::IS_APPROVED]);
        return $status;
    }
    
    /**
     * function is used to reject beautician
     * @param int $userId
     * @return boolean $status | True in case of update else False
     */
    public static function rejectBeautician($userId) {
        
        $declinedTime = date('Y-m-d H:i:s');
        $status = User::where('id', $userId)->update(['admin_approval_status'=>  User::IS_DISAPPROVED, 'profile_declined_at'=>$declinedTime]);
        return $status;
    }
    
    /**
     * function is used to get customer list
     * @param array $data
     * @return type
     */
    public function getCustomerListWeb($data=array()) {
        $resultCount = 0;
        
        $totalResult = DB::table($this->table)
                            ->where($this->table.'.status', static::IS_ACTIVE)
                            ->select($this->table.'.*');
        $searchQuery = '';
        if (isset($data['q'])) {
            $searchQuery = $data['q'];
        }
        $userType = $data['user_type'];
        $totalResult->where($this->table.'.user_type', $userType);
        if ($data) {
            if (isset($searchQuery) && !empty($searchQuery)) {
                $totalResult->where(function($query) use($searchQuery) {
                        $query->orWhere($this->table.'.email', 'like', '%' . $searchQuery . '%');
                        $query->orWhere($this->table.'.first_name', 'like', '%' . $searchQuery . '%');
                    });
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
     * function is used to get suspended user list
     * @param array $data
     * @return type
     */
    public function getSuspendedUserListWeb($data=array()) {
        $resultCount = 0;
        
        $totalResult = DB::table($this->table)
                            ->where($this->table.'.user_type', '!=', User::IS_ADMIN)
                            ->where($this->table.'.status', User::IS_INACTIVE)
                            ->select($this->table.'.*');
        $searchQuery = '';
        if (isset($data['q'])) {
            $searchQuery = $data['q'];
        }
        
        if ($data) {
            if (isset($searchQuery) && !empty($searchQuery)) {
                $totalResult->where(function($query) use($searchQuery) {
                        $query->orWhere($this->table.'.email', 'like', '%' . $searchQuery . '%');
                        $query->orWhere($this->table.'.last_name', 'like', '%' . $searchQuery . '%');
                    });
            }
            $resultCount = $totalResult->get();
            $totalResult->orderby($this->table.'.email', 'asc');
            $result = $totalResult->skip($data['offset'])->take($data['limit'])->get();
        } else {
            $result = $totalResult->get();
            $resultCount = $result;
        }
        $resultCount = count($resultCount);
        return array('count' => $resultCount, 'result' => $result);
    }
    
    /**
     * function is used to get flagged user list
     * @param array $data
     * @return type
     */
    public function getFlaggedUserListWeb($data=array()) {
        $resultCount = 0;
        
        $totalResult = DB::table($this->table)
                            ->where($this->table.'.is_flagged', static::IS_FLAGGED)
                            ->where($this->table.'.status', static::IS_ACTIVE)
                            ->select($this->table.'.*')
                            ->orderBy($this->table.'.id', 'desc');
        $searchQuery = '';
        if (isset($data['q'])) {
            $searchQuery = $data['q'];
        }
        
        if ($data) {
            if (isset($searchQuery) && !empty($searchQuery)) {
                $totalResult->where(function($query) use($searchQuery) {
                        $query->orWhere($this->table.'.email', 'like', '%' . $searchQuery . '%');
                        $query->orWhere($this->table.'.last_name', 'like', '%' . $searchQuery . '%');
                    });
            }
            $resultCount = $totalResult->get();
            $totalResult->orderby($this->table.'.email', 'asc');
            $result = $totalResult->skip($data['offset'])->take($data['limit'])->get();
        } else {
            $result = $totalResult->get();
            $resultCount = $result;
        }
        $resultCount = count($resultCount);
        return array('count' => $resultCount, 'result' => $result);
    }
    
    /**
     * function is used to fetch user's list who have reported a given user
     * @param int $userId
     * @return array
     */
    public static function fetchUserReportedByList($userId) {
        $userObj = User::join('flagged_users as fu', 'fu.flagged_user', '=', 'users.id')
                        ->join('users as u2', 'u2.id', '=', 'fu.flagged_by')
                        ->join('flag_reasons', 'flag_reasons.id', '=', 'fu.flag_reason_id')
                        ->where('users.id', $userId)
                        ->select('users.*', 'fu.id as flagged_id', 'flag_reasons.reason', 'u2.user_type as flagged_by_user_type',
                                DB::raw('DATE(fu.created_at) as flagged_on'), 'u2.first_name as flagged_by_first_name', 'u2.last_name as flagged_by_last_name', 
                                'u2.email as flagged_by_email')
                        ->orderBy('fu.created_at', 'desc')->get()->toArray();
        return $userObj;
    }
    
    /**
     * function is used to fetch customer detail
     * @param int $userId
     * @return Object
     */
    public static function fetchCustomerDetail($userId) {
        
        $userObj = User::where('users.id', $userId)
                        ->select('users.*')
                        ->first();
        return $userObj;
    }
    
    /**
     * function is used to get referred user list
     * @param array $data
     * @return type
     */
    public function getReferredUserListWeb($data=array()) {
        $resultCount = 0;
        
        $totalResult = DB::table($this->table)
                            ->join($this->table.' as u1', 'u1.referral_code', '=', $this->table.'.referral_code_used')
                            ->leftjoin('customer_bookings', function($query){
                                $query->on('customer_bookings.customer_id', '=', 'u1.id')
                                      ->on('customer_bookings.referred_user_id', '=', $this->table.'.id');
                            })
                            ->where($this->table.'.user_type', '!=', User::IS_ADMIN)
                            ->where($this->table.'.status', User::IS_ACTIVE)
                            ->where($this->table.'.referral_code_used', '!=', "")
                            ->select($this->table.'.*', DB::raw('CONCAT_WS(" ",u1.first_name, u1.last_name) as referred_by_name'),'u1.bank_acc_no','u1.bank_bsb_no','u1.bank_username',
                                    'customer_bookings.referred_user_id', 'customer_bookings.actual_cost', 'customer_bookings.travel_cost', 'customer_bookings.commission_percent');
        $searchQuery = '';
        if (isset($data['q'])) {
            $searchQuery = $data['q'];
        }

        if ($data) {
            if (isset($searchQuery) && !empty($searchQuery)) {
                $totalResult->where(function($query) use($searchQuery) {
                        $query->orWhere($this->table.'.email', 'like', '%' . $searchQuery . '%');
                        $query->orWhere($this->table.'.first_name', 'like', '%' . $searchQuery . '%');
                    });
            }
            $resultCount = $totalResult->get();
            $totalResult->orderby($this->table.'.email', 'asc');
            $result = $totalResult->skip($data['offset'])->take($data['limit'])->get();
        } else {
            $result = $totalResult->get();
            $resultCount = $result;
        }
        $resultCount = count($resultCount);
        return array('count' => $resultCount, 'result' => $result);
    }
    
    /**
     * function is used to get customer revenue list
     * @param array $data
     * @return type
     */
    public function getCustomerRevenueListWeb($data=array()) {
        $resultCount = 0;
        
        $totalResult = DB::table($this->table)
                            ->leftjoin('customer_bookings', 'customer_bookings.customer_id', '=', $this->table.'.id')
                            ->where($this->table.'.user_type', '=', User::IS_CUSTOMER)
                            ->where($this->table.'.status', User::IS_ACTIVE)
                            ->select($this->table.'.*', DB::raw('SUM(customer_bookings.actual_cost + customer_bookings.travel_cost) as total_cost'))
                            ->groupBy($this->table.'.id')
                            ->orderBy('total_cost', 'desc');
        if(!empty($data['month']) && !empty($data['year'])) {
            $totalResult->whereRaw('YEAR(customer_bookings.start_datetime) = '.$data['year'])
                        ->whereRaw('MONTH(customer_bookings.start_datetime) = '.$data['month']);
        }
        if(!empty($data['suburb'])) {
            $totalResult->where($this->table.'.suburb', $data['suburb']);
        }
        $searchQuery = '';
        if (isset($data['q'])) {
            $searchQuery = $data['q'];
        }
        
        if ($data) {
            if (isset($searchQuery) && !empty($searchQuery)) {
                $totalResult->where(function($query) use($searchQuery) {
                        $query->orWhere($this->table.'.email', 'like', '%' . $searchQuery . '%');
                        $query->orWhere($this->table.'.first_name', 'like', '%' . $searchQuery . '%');
                    });
            }

            $resultCount = $totalResult->get();
            $totalResult->orderby($this->table.'.email', 'asc');
            $result = $totalResult->skip($data['offset'])->take($data['limit'])->get();
        } else {
            $result = $totalResult->get();
            $resultCount = $result;
        }
        $resultCount = count($resultCount);
        return array('count' => $resultCount, 'result' => $result);
    }
    
    /**
     * function is used to get beauty pro revenue list
     * @param array $data
     * @return type
     */
    public function getBeauticianRevenueListWeb($data=array()) {
        $resultCount = 0;
        
        $totalResult = DB::table($this->table)
                            ->leftjoin('customer_bookings', 'customer_bookings.beautician_id', '=', $this->table.'.id')
                            ->where($this->table.'.user_type', '=', User::IS_BEAUTICIAN)
                            ->where($this->table.'.status', User::IS_ACTIVE)
                            ->select($this->table.'.*', DB::raw('SUM(customer_bookings.actual_cost + customer_bookings.travel_cost) as total_cost'))
                            ->groupBy($this->table.'.id')
                            ->orderBy('total_cost', 'desc');
        if(!empty($data['month']) && !empty($data['year'])) {
            $totalResult->whereRaw('YEAR(customer_bookings.start_datetime) = '.$data['year'])
                        ->whereRaw('MONTH(customer_bookings.start_datetime) = '.$data['month']);
        }
        if(!empty($data['suburb'])) {
            $totalResult->where($this->table.'.suburb', $data['suburb']);
        }
        $searchQuery = '';
        if (isset($data['q'])) {
            $searchQuery = $data['q'];
        }
        
        if ($data) {
            if (isset($searchQuery) && !empty($searchQuery)) {
                $totalResult->where(function($query) use($searchQuery) {
                        $query->orWhere($this->table.'.email', 'like', '%' . $searchQuery . '%');
                        $query->orWhere($this->table.'.first_name', 'like', '%' . $searchQuery . '%');
                    });
            }

            $resultCount = $totalResult->get();
            $totalResult->orderby($this->table.'.email', 'asc');
            $result = $totalResult->skip($data['offset'])->take($data['limit'])->get();
        } else {
            $result = $totalResult->get();
            $resultCount = $result;
        }
        $resultCount = count($resultCount);
        return array('count' => $resultCount, 'result' => $result);
    }
    
    /**
     * function is used to get total completed jobs of all beautician
     * @param int $currentMonth
     * @return type
     */
    public static function getTotalCompletedJobs($currentMonth, $currentYear) {
        return  DB::table(static::$currentTable)
                    ->leftjoin('customer_bookings', 'customer_bookings.beautician_id', '=', static::$currentTable.'.id')
                    ->whereIn('customer_bookings.status', array(CustomerBooking::IS_PAYMENT_DONE, CustomerBooking::DISPUTE_REJECTED_BY_ADMIN))
                    ->where(static::$currentTable.'.user_type', User::IS_BEAUTICIAN)
                    ->where(static::$currentTable.'.status', User::IS_ACTIVE)
                    ->whereRaw('MONTH(customer_bookings.end_datetime) = '.$currentMonth)
                    ->whereRaw('YEAR(customer_bookings.end_datetime) = '.$currentYear)
                    ->select(static::$currentTable.'.id', DB::raw('COUNT(customer_bookings.id) as total_completed_jobs'))
                    ->groupBy(static::$currentTable.'.id')
                    ->get()->toArray();
    }
    
    /**
     * function is used to get total cancelled jobs of all beautician
     * @param int $currentMonth
     */
    public static function getTotalCancelledJobs($currentMonth, $currentYear) {
        return  DB::table(static::$currentTable)
                    ->leftjoin('customer_bookings', 'customer_bookings.beautician_id', '=', static::$currentTable.'.id')
                    ->where('customer_bookings.status', CustomerBooking::IS_CANCELLED)
                    ->where(static::$currentTable.'.user_type', User::IS_BEAUTICIAN)
                    ->where(static::$currentTable.'.status', User::IS_ACTIVE)
                    ->whereRaw('MONTH(end_datetime) = '.$currentMonth)
                    ->whereRaw('YEAR(customer_bookings.end_datetime) = '.$currentYear)
                    ->select(static::$currentTable.'.id', DB::raw('COUNT(customer_bookings.id) as total_cancelled_jobs'))
                    ->groupBy(static::$currentTable.'.id')
                    ->get()->toArray();
    }
    
    /**
     * function is used to get total disputed services by all beautician
     * @param int $currentMonth
     */
    public static function getTotalDisputedJobs($currentMonth, $currentYear) {
        return  DB::table(static::$currentTable)
                    ->leftjoin('customer_bookings', 'customer_bookings.beautician_id', '=', static::$currentTable.'.id')
                    ->where('customer_bookings.status', CustomerBooking::IS_DISPUTED_PAYMENT_HELD)
                    ->where(static::$currentTable.'.user_type', User::IS_BEAUTICIAN)
                    ->where(static::$currentTable.'.status', User::IS_ACTIVE)
                    ->whereRaw('MONTH(end_datetime) = '.$currentMonth)
                    ->whereRaw('YEAR(customer_bookings.end_datetime) = '.$currentYear)
                    ->select(static::$currentTable.'.id', DB::raw('COUNT(customer_bookings.id) as total_disputed_jobs'))
                    ->groupBy(static::$currentTable.'.id')
                    ->get()->toArray();
    }
    
    /**
     * function is used to get total revenue of all beautician
     * @param int $currentMonth
     * @return type
     */
    public static function getTotalRevenue($currentMonth, $currentYear) {
        $arrStatus = [CustomerBooking::IS_DISPUTED_PAYMENT_HELD, CustomerBooking::IS_PAYMENT_DONE, CustomerBooking::DISPUTE_REJECTED_BY_ADMIN];
        return  DB::table(static::$currentTable)
                    ->leftjoin('customer_bookings', 'customer_bookings.beautician_id', '=', static::$currentTable.'.id')
                    ->whereIn('customer_bookings.status', $arrStatus)
                    ->where(static::$currentTable.'.user_type', User::IS_BEAUTICIAN)
                    ->where(static::$currentTable.'.status', User::IS_ACTIVE)
                    ->whereRaw('MONTH(end_datetime) = '.$currentMonth)
                    ->whereRaw('YEAR(customer_bookings.end_datetime) = '.$currentYear)
                    ->select(static::$currentTable.'.id', DB::raw('SUM(customer_bookings.actual_cost + customer_bookings.travel_cost) as total_cost'))
                    ->groupBy(static::$currentTable.'.id')
                    ->get()->toArray();
    }
    
    /**
     * function is used to get total completed services
     * @param int $currentMonth
     * @param int $currentYear
     * @return type
     */
    public static function getTotalCompletedServices($id, $currentMonth, $currentYear) {
        $arrStatus = [CustomerBooking::IS_DISPUTED_PAYMENT_HELD, CustomerBooking::IS_PAYMENT_DONE, CustomerBooking::DISPUTE_REJECTED_BY_ADMIN];
        return  DB::table(static::$currentTable)
                    ->leftjoin('customer_bookings', 'customer_bookings.beautician_id', '=', static::$currentTable.'.id')
                    ->leftjoin('services', 'services.id', '=', 'customer_bookings.parent_service_id')
                    ->whereIn('customer_bookings.status', $arrStatus)
                    ->where(static::$currentTable.'.user_type', User::IS_BEAUTICIAN)
                    ->where(static::$currentTable.'.status', User::IS_ACTIVE)
                    ->where(static::$currentTable.'.id', $id)
                    ->whereRaw('MONTH(end_datetime) = '.$currentMonth)
                    ->whereRaw('YEAR(customer_bookings.end_datetime) = '.$currentYear)
                    ->select(static::$currentTable.'.id', 'services.name', DB::raw('COUNT(customer_bookings.id) as total_service'))
                    ->groupBy('services.id')
                    ->get()->toArray();
    }
    
    /**
     * function is used to get average rating per month
     * @param int $currentYear
     */
    public static function getRatingPerMonth($id, $currentYear) {
        $arrStatus = [CustomerBooking::IS_DISPUTED_PAYMENT_HELD, CustomerBooking::IS_PAYMENT_DONE, CustomerBooking::DISPUTE_REJECTED_BY_ADMIN];
        return  DB::table(static::$currentTable)
                    ->join('customer_bookings', 'customer_bookings.beautician_id', '=', static::$currentTable.'.id')
                    ->join('booking_ratings', function($query){
                        $query->on('booking_ratings.rated_to', '=', static::$currentTable.'.id')
                              ->on('booking_ratings.customer_booking_id', '=', 'customer_bookings.id');
                    })
                    ->whereIn('customer_bookings.status', $arrStatus)
                    ->where(static::$currentTable.'.user_type', User::IS_BEAUTICIAN)
                    ->where(static::$currentTable.'.status', User::IS_ACTIVE)
                    ->where(static::$currentTable.'.id', $id)
                    ->whereRaw('YEAR(customer_bookings.end_datetime) = '.$currentYear)
                    ->select(static::$currentTable.'.id', DB::raw('MONTH(customer_bookings.end_datetime) as monthly'), DB::raw('SUM(booking_ratings.rating)/COUNT(booking_ratings.id) as avg_rating'))
                    ->groupBy('monthly')
                    ->get()->toArray();
    }
}
