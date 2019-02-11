<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: UserServiceProvider.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Providers\Admin;

use App\Models\User;
use App\Models\Service;
use App\Models\BeauticianDetail;
use App\Models\BeauticianService;
use Illuminate\Support\Facades\Auth;
use App\Utilities\Mail;
use App\Providers\BaseServiceProvider;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use \App\Models\FlaggedUser;

/**
 * UserServiceProvider class contains methods for user management
 */
class UserServiceProvider extends BaseServiceProvider {

    /**
     * Change user password
     *
     * @param type $data
     * @return type
     */
    public static function changePassword($data) {
        try {
            $user = Auth::user();

            if (!\Hash::check($data['old_password'], $user->password)) {
                static::$data['message'] = trans('messages.incorrect_old_password');
                static::$data['success'] = false;
                static::$data['status_code'] = \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST;
                return static::$data;
            }

            $user->password = \Hash::make($data['password']);
            $user->reset_password = config('constants.RESET_PASSWORD.blocked');
            $user->save();

            static::$data['success'] = true;
            static::$data['message'] = trans('messages.password_changed');
        } catch (\Exception $e) {

            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * Send temporaryPassword
     * 
     * @param type $data
     * @return type
     */
    public static function temporaryPassword($data) {


        try {
            $currentDate = date('Y-m-d');

            $user = new User();
            $user = User::where('email', '=', $data['email'])->where('user_type', '=', $data['user_type'])->first();

            if ($user) {
                if (($user->reset_password_count < config('constants.PASSWORD_RECOVERY_LIMIT')) && ($user->reset_password_date == $currentDate) || ($user->reset_password_date != $currentDate)) {
                    $count = $user->reset_password_count;
                    if ($user->reset_password_date == $currentDate) {
                        $user->reset_password_count = $count + 1;
                        $user->reset_password_date = $currentDate;
                        $user->save();
                    } else {
                        $user->reset_password_count = 1;
                        $user->reset_password_date = $currentDate;
                        $user->save();
                    }
                    $password = str_random(8);
                    $hashedPassword = \Hash::make($password);
                    static::$mailData['view'] = 'email.admin.reset_password';
                    static::$mailData['data'] = array('password' => $password);
                    static::$mailData['user'] = $user;
                    static::$mailData['subject'] = config('constants.SUBJECT.admin_forgot_password');

                    $mail = new Mail();
                    $status = $mail->sendMail(static::$mailData);

                    if ($status) {
                        $user->reset_password = config('constants.RESET_PASSWORD.active');
                        $user->password = $hashedPassword;
                        $user->save();
                        static::$data['success'] = true;
                        static::$data['message'] = trans('messages.forgot_password_email');
                    } else {
                        static::$data['success'] = false;
                        static::$data['message'] = trans('messages.exception_msg');
                    }
                } else {
                    static::$data['success'] = false;
                    static::$data['message'] = trans('messages.attempt_limit_exceed');
                }
            } else {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.user_not_exist');
            }
        } catch (\Exception $e) {

            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * getDateTimeToUtc .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function getDateTimeToUtc($data) {

        $dt = new DateTime($data, new \DateTimeZone('GMT'));
        $dt->setTimezone(new \DateTimeZone('UTC'));
        return $dt->format('Y-m-d H:i:s');
    }

    /**
     * getDateTimeToUtc .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function getDateTimeToLocalTimeZone($data, $timeZone) {

        $dt = new DateTime($data, new \DateTimeZone('UTC'));
        $dt->setTimezone(new \DateTimeZone($timeZone));
        return $dt->format('Y-m-d H:i:s');
    }

    /**
     * get Rejected Beautician List
     * @return type
     */
    public static function getRejectedBeauticianList() {
        $userModel = new User();
        $data = array();
        $search = '';
        $approvedStatus = User::IS_DISAPPROVED;
        $input = Input::all();
        if (isset($input['search']['value'])) {
            $search = $input['search']['value'];
        }
        if (!$search) {
            $results = $userModel->getBeauticianListWeb(array('user_type' => 2, 'approvedStatus' => $approvedStatus, 'limit' => $input['length'], 'offset' => $input['start']));
        } else {
            $results = $userModel->getBeauticianListWeb(array('user_type' => 2, 'approvedStatus' => $approvedStatus, 'q' => $search, 'limit' => $input['length'], 'offset' => $input['start']));
        }
        foreach ($results['result'] as $result) {
            $data[] = array(
                $result->id,
                $result->email,
                ucwords(trim($result->first_name." ".$result->last_name)),
                $result->admin_approval_status
            );
        }
        return array('data' => $data, 'recordsTotal' => $results['count'], "recordsFiltered" => $results['count']);
    }

    /**
     * function is used get approved beautician list
     * @return type
     */
    public static function getApprovedBeauticianList() {
        $userModel = new User();
        $data = array();
        $search = '';
        $approvedStatus = User::IS_APPROVED;
        $input = Input::all();
        if (isset($input['search']['value'])) {
            $search = $input['search']['value'];
        }
        if (!$search) {
            $results = $userModel->getBeauticianListWeb(array('user_type' => 2, 'approvedStatus' => $approvedStatus, 'limit' => $input['length'], 'offset' => $input['start']));
        } else {
            $results = $userModel->getBeauticianListWeb(array('user_type' => 2, 'approvedStatus' => $approvedStatus, 'q' => $search, 'limit' => $input['length'], 'offset' => $input['start']));
        }
        foreach ($results['result'] as $result) {
            $data[] = array(
                $result->id,
                $result->email,
                ucwords(trim($result->first_name." ".$result->last_name)),
                $result->admin_approval_status
            );
        }

        return array('data' => $data, 'recordsTotal' => $results['count'], "recordsFiltered" => $results['count']);
    }

    /**
     * function is used to get beautician list for approval
     * @return type
     */
    public static function getBeauticianListForApproval() {
        $userModel = new User();
        $data = array();
        $search = '';
        $approvedStatus = User::IS_APPROVAL_PENDING;
        $input = Input::all();
        if (isset($input['search']['value'])) {
            $search = $input['search']['value'];
        }
        if (!$search) {
            $results = $userModel->getBeauticianListWeb(array('user_type' => 2, 'approvedStatus' => $approvedStatus, 'limit' => $input['length'], 'offset' => $input['start']));
        } else {
            $results = $userModel->getBeauticianListWeb(array('user_type' => 2, 'approvedStatus' => $approvedStatus, 'q' => $search, 'limit' => $input['length'], 'offset' => $input['start']));
        }
        foreach ($results['result'] as $result) {
            $data[] = array(
                $result->id,
                $result->email,
                ucwords(trim($result->first_name." ".$result->last_name)),
                $result->admin_approval_status
            );
        }

        return array('data' => $data, 'recordsTotal' => $results['count'], "recordsFiltered" => $results['count']);
    }

    /**
     * function is used to send approval mail
     * @param int $userId
     */
    public static function sendApprovedMail($userId) {
        try {
            $userObj = User::where('id', $userId)->first();
            $arrData = ['status' => false, 'message'];
            if ($userObj) {
                //send successful registration email to beautician and admin
                $subject = trans('messages.beautician.approved');

                \Mail::send('email.beautician.approved', ['userObj' => $userObj], function($message) use ($userObj, $subject) {
                    $message->to($userObj->email)->subject($subject);
                    $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                });
                if (count(\Mail::failures()) == 0) {
                    $arrData['status'] = true;
                    $arrData['message'] = trans('messages.beautician.approved_mail_success');
                } else {
                    $arrData['message'] = trans('messages.beautician.approved_mail_failure');
                }
            } else {
                $arrData['message'] = trans('messages.beautician.not_exist');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
            $arrData['message'] = $e;
        }
        return $arrData;
    }

    /**
     * function is used to send rejected mail
     * @param int $userId
     */
    public static function sendRejectedMail($userId) {

        try {
            $userObj = User::where('id', $userId)->first();
            $arrData = ['status' => false, 'message'];
            if ($userObj) {
                //send successful registration email to beautician and admin
                $subject = trans('messages.beautician.rejected');

                \Mail::send('email.beautician.rejected', ['userObj' => $userObj], function($message) use ($userObj, $subject) {
                    $message->to($userObj->email)->subject($subject);
                    $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                });
                if (count(\Mail::failures()) == 0) {
                    $arrData['status'] = true;
                    $arrData['message'] = trans('messages.beautician.rejected_mail_success');
                } else {
                    $arrData['message'] = trans('messages.beautician.rejected_mail_failure');
                }
            } else {
                $arrData['message'] = trans('messages.beautician.not_exist');
            }
        } catch (\Exception $e) {

            static::setExceptionError($e);
        }
    }

    /**
     * function is used to get customer list
     * @return type
     */
    public static function getCustomerList() {
        $userModel = new User();
        $data = array();
        $search = '';
        $input = Input::all();
        if (isset($input['search']['value'])) {
            $search = $input['search']['value'];
        }
        if (!$search) {
            $results = $userModel->getCustomerListWeb(array('user_type' => User::IS_CUSTOMER, 'limit' => $input['length'], 'offset' => $input['start']));
        } else {
            $results = $userModel->getCustomerListWeb(array('user_type' => User::IS_CUSTOMER, 'q' => $search, 'limit' => $input['length'], 'offset' => $input['start']));
        }
        foreach ($results['result'] as $result) {
            $data[] = array(
                $result->id,
                $result->email,
                ucwords(trim($result->first_name." ".$result->last_name))
            );
        }

        return array('data' => $data, 'recordsTotal' => $results['count'], "recordsFiltered" => $results['count']);
    }

    /**
     * Get list of users
     *
     * @return type
     */
    public static function getUsers($userType, $userStatus = 0) {

        try {
            $i = 0;
            $feedbackArray = array();
            $query = User::where(function($query) use($userType) {
                        $query->where('users.user_type', '!=', User::IS_ADMIN)
                                ->where('users.user_type', '=', $userType);
                    });

            $query->where('users.admin_approval_status', $userStatus);
            $query->where('users.status', User::IS_ACTIVE);
            $query->select('users.first_name', 'users.last_name', 'users.email');
            $query->groupBy('users.id');
            $users = $query->orderBy('users.email', 'ASC')->get();

            foreach ($users as $user) {

                $userData['name'] = ucfirst($user->first_name) . ' ' . ucfirst($user->last_name);
                $userData['email'] = $user->email;
                $feedbackArray[] = $userData;
                $i++;
            }

            $collection = collect($feedbackArray);
            static::$data['users'] = $collection;
            static::$data['success'] = true;
            static::$data['message'] = trans('messages.record_listed');
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * create data csv .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function getDataCsv($data, $header, $filename) {

        $headers = $header;
        $fp = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');
        fputcsv($fp, $headers);


        foreach ($data as $value) {

            fputcsv($fp, $value);
        }
    }

    /**
     * function is used to get suspended user list
     * @return type
     */
    public static function getSuspendedUserList() {
        $userModel = new User();
        $data = array();
        $search = '';
        $input = Input::all();
        if (isset($input['search']['value'])) {
            $search = $input['search']['value'];
        }
        if (!$search) {
            $results = $userModel->getSuspendedUserListWeb(array('limit' => $input['length'], 'offset' => $input['start']));
        } else {
            $results = $userModel->getSuspendedUserListWeb(array('q' => $search, 'limit' => $input['length'], 'offset' => $input['start']));
        }

        foreach ($results['result'] as $result) {
            $data[] = array(
                $result->id,
                $result->email,
                ucwords(trim($result->first_name." ".$result->last_name)),
                $result->user_type
            );
        }

        return array('data' => $data, 'recordsTotal' => $results['count'], "recordsFiltered" => $results['count']);
    }

    /**
     * function is used to suspend or unsuspend an user
     * @param int $userId
     * @return type
     */
    public static function suspendUnsuspendUser($userId) {
        try {
            $userObj = User::where('id', $userId)->first();
            static::$data['success'] = false;
            if ($userObj->status == User::IS_ACTIVE) {
                $userObj->status = User::IS_INACTIVE;
            } else {
                $userObj->status = User::IS_ACTIVE;
            }

            static::$data['success'] = $userObj->save();
            //unflag user after suspending the user
            if (static::$data['success'] && $userObj->status == User::IS_INACTIVE) {
                static::unflagUser($userId);
            }
            static::$data['user'] = $userObj;
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to get flagged user list
     * @return type
     */
    public static function getFlaggedUserList() {
        $userModel = new User();
        $data = array();
        $search = '';
        $input = Input::all();
        if (isset($input['search']['value'])) {
            $search = $input['search']['value'];
        }
        if (!$search) {
            $results = $userModel->getFlaggedUserListWeb(array('limit' => $input['length'], 'offset' => $input['start']));
        } else {
            $results = $userModel->getFlaggedUserListWeb(array('q' => $search, 'limit' => $input['length'], 'offset' => $input['start']));
        }

        foreach ($results['result'] as $result) {

            $data[] = array(
                $result->id,
                $result->email,
                ucwords(trim($result->first_name." ".$result->last_name)),
                $result->user_type
            );
        }

        return array('data' => $data, 'recordsTotal' => $results['count'], "recordsFiltered" => $results['count']);
    }

    /**
     * function is used to get all the users who have flagged the given user
     * @param int $userId
     * @return array
     */
    public static function getUserReportedByList($userId) {
        try {
            $userObj = User::fetchUserReportedByList($userId);

            if (count($userObj) > 0) {
                static::$data['user'] = $userObj;
                static::$data['success'] = true;
            } else {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.user.not_exist');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to unflag a flagged user
     * @param int $userId
     * @return array
     */
    public static function unflagUser($userId) {
        try {
            DB::beginTransaction();
            static::$data['success'] = User::where('id', $userId)->update(['is_flagged' => User::IS_NOT_FLAGGED]);

            if (static::$data['success']) {
                FlaggedUser::where('flagged_user', $userId)->delete();
                static::$data['success'] = true;
                if (empty(static::$data['message'])) {
                    static::$data['message'] = trans('messages.user.unflagged_success');
                }
            } else {
                static::$data['message'] = trans('messages.user.not_exist');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to get referred user list
     * @return type
     */
    public static function getReferredUserList() {
        $userModel = new User();
        $data = array();
        $search = '';
        $input = Input::all();
        if (isset($input['search']['value'])) {
            $search = $input['search']['value'];
        }
        if (!$search) {
            $results = $userModel->getReferredUserListWeb(array('limit' => $input['length'], 'offset' => $input['start']));
        } else {
            $results = $userModel->getReferredUserListWeb(array('q' => $search, 'limit' => $input['length'], 'offset' => $input['start']));
        }


        foreach ($results['result'] as $result) {
            $cashback = 0;
            if(!empty($result->referred_user_id)) {
                $cashback = $result->actual_cost + $result->travel_cost;
                $cashback = ($cashback * $result->commission_percent) / 100;
            }
            $data[] = array(
                $result->id,
                $result->email,
                trim(ucwords($result->first_name).' '.ucwords($result->last_name)),
                $result->user_type,
                ucwords($result->referred_by_name),
                $result->bank_username,
                $result->bank_bsb_no,
                 $result->bank_acc_no,
                '$'.$cashback
            );

        }

        return array('data' => $data, 'recordsTotal' => $results['count'], "recordsFiltered" => $results['count']);
    }

    /**
     * function is used to get customer revenue list
     * @return type
     */
    public static function getCustomerRevenue($month = '', $year = '', $suburb = '') {
        $userModel = new User();
        $data = array();
        $search = '';
        $input = Input::all();

        if (isset($input['search']['value'])) {
            $search = $input['search']['value'];
        }
        if (!empty($input['suburb'])) {
            $suburb = $input['suburb'];
        }
        if (!empty($input['month'])) {
            $month = $input['month'];
        }
        if (!empty($input['year'])) {
            $year = $input['year'];
        }
        if (!$search) {
            $results = $userModel->getCustomerRevenueListWeb(array('limit' => $input['length'], 'offset' => $input['start'], 'month' => $month, 'year' => $year, 'suburb' => $suburb));
        } else {
            $results = $userModel->getCustomerRevenueListWeb(array('q' => $search, 'limit' => $input['length'], 'offset' => $input['start'], 'month' => $month, 'year' => $year, 'suburb' => $suburb));
        }

        foreach ($results['result'] as $result) {
            $data[] = array(
                $result->id,
                $result->email,
                ucwords(trim($result->first_name." ".$result->last_name)),
                $result->suburb,
                $result->total_cost
            );
        }

        return array('data' => $data, 'recordsTotal' => $results['count'], "recordsFiltered" => $results['count']);
    }

    public static function fetchUsersSuburb() {
        return User::where('status', User::IS_ACTIVE)->where('user_type', '!=', User::IS_ADMIN)
                        ->where('suburb', '!=', '')
                        ->selectRaw('DISTINCT(suburb) as suburb')->get()->toArray();
    }

    
    /**
     * function is used to get admin settings
     * @return type
     */
    public static function getSettings() {
        $data = array();

        $results = DB::table('admin_settings')->where('config_key','!=','global_commission')->get();

        foreach ($results as $result) {
            $data[] = array(
                $result->config_key,
                $result->config_value,
                $result->id
            );
        }

        return array('data' => $data, 'recordsTotal' => count($results), "recordsFiltered" => count($results));
    }

     /**
     * function is used to get admin settings
     * @return type
     */
    public static function modifyAdminSetting($input) {
        try
        {
          DB::table('admin_settings')->where('id',$input['id'])->update(['config_value' => $input['value']]);

        } catch (\Exception $e) {
            DB::rollback();
            static::setExceptionError($e);
        }

        return static::$data;
    }


    /**
     * function is used to get the list of states along with their commission fee of the beautician
     * @return type
     */
    public static function getStates() {
        try
        {
          $states = User::where('user_type','=',User::IS_BEAUTICIAN)->groupBy('state')->orderBy('state')->select('state','commission_percent')->get();
          static::$data['states'] = $states;

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }


    /**
     * function is used to save state commissions
     * @return type
     */
    public static function saveStateCommissions($input) {
        try
        {
          static::resetServiceCommissionsPercent();
          DB::table('admin_settings')->where('config_key','global_commission')->update(['config_value' => 0]);
          $statesArr = $input['state'];

          foreach ($statesArr as $key => $value) {
             User::where('state',$key)->update(['commission_percent' => $value]);  
          }

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }


    /**
     * function is used to get the list of services along with their commission fee of the beautician
     * @return type
     */
    public static function getServiceCommissions() {
        try
        {
          $services = Service::whereNull('parent_id')
                              ->select('name','id','commission_percent')->orderBy('name')->get();
          static::$data['services'] = $services;

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }


    /**
     * function is used to get the list of services along with their commission fee of the beautician
     * @return type
     */
    public static function getBeauticianServiceCommissions($beauticianId) {
        try
        {
          $services = BeauticianService::join('services',function($join) use ($beauticianId){
                                    $join->on('services.id','=','beautician_services.parent_service_id')
                                         ->where('beautician_services.beautician_id',$beauticianId);
                                })
                              ->select('services.name','beautician_services.premium_commission_percent','services.id as service_id','beautician_services.beautician_id')->groupBy('beautician_services.parent_service_id')->orderBy('services.name')->get();
          static::$data['services'] = $services;

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    


    /**
     * function is used to save service commissions
     * @return type
     */
    public static function saveServiceCommissions($input) {
        try
        {
          static::resetStateCommissionsPercent();
          DB::table('admin_settings')->where('config_key','global_commission')->update(['config_value' => 0]);


          $serviceArr = $input['service'];

          foreach ($serviceArr as $key => $value) {
             Service::where('id',$key)->update(['commission_percent' => $value]);  
          }

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to save premium service commissions
     * @return type
     */ 
    public static function savePremiumServiceCommissions($input) {
        try
        {

          $beauticianServiceArr = $input['beautician_service'];

          foreach ($beauticianServiceArr as $key => $value) {
            foreach ($value as $parentServiceId => $commissionPercent) {
                
                BeauticianService::where('parent_service_id',$parentServiceId)->where('beautician_id',$key)
                                   ->update(['premium_commission_percent' => $commissionPercent]);
            }
          }

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }


     /**
     * function is used to save premium service commissions
     * @return type
     */ 
    public static function removePremiumCommissions($input) {
        try
        {

          $beauticianId = $input['beauticianId'];

          BeauticianService::where('beautician_id',$beauticianId)
                                   ->update(['premium_commission_percent' => NULL]);
         

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    
    
    /**
     * function is used to get global beauty pro percentage
     * @return type
     */
    public static function getBeautyProGlobalPercent() {
        try
        {
          $globalPercent = DB::table('admin_settings')
                          ->where('config_key','global_commission')->pluck('config_value')->first();

          static::$data['percent'] = $globalPercent;

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to set global beauty pro percentage
     * @return type
     */
    public static function setBeautyProGlobalPercent($input) {
        try
        {
          static::resetServiceCommissionsPercent();
          User::where('user_type',User::IS_BEAUTICIAN)->update(['commission_percent' => $input['global_percent']]);
          DB::table('admin_settings')->where('config_key','global_commission')->update(['config_value' => $input['global_percent']]);

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to set global premium beauty pro percentage
     * @return type
     */
    public static function setBeautyProPremiumGlobalPercent($input) {
        try
        {
          BeauticianService::where('beautician_id',$input['beauticianId'])
                            ->update(['premium_commission_percent' => $input['commissionPercent']]);
                            
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }


    

    public static function resetStateCommissionsPercent()
    {
         User::where('user_type',User::IS_BEAUTICIAN)->update(['commission_percent' => 0 ]);
    }

    public static function resetServiceCommissionsPercent()
    {
        Service::whereNull('parent_id')->update(['commission_percent' => 0]);  
    }


    /**
     * function is used to get the list of all the beauty pros whose services are defined
     * @return type
     */
    public static function getBeautyProListing() {
        try
        {
          $beauticians = BeauticianDetail::join('beautician_services','beautician_services.beautician_id','=','beautician_details.user_id')->groupBy('beautician_services.beautician_id')->select('business_name','user_id as id')->get();
          static::$data['beauticians'] = $beauticians;

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }


      /**
     * function is used to get the list of premium beauty pros
     * @return type
     */
    public static function getPremiumBeautyPros() {
        try
        {
         
          $premiumBeauticians = BeauticianService::join('beautician_details',function($join){
                                  $join->on('beautician_details.user_id','=','beautician_services.beautician_id')
                                 ->where('premium_commission_percent','!=',0);
                            })
                            ->select('beautician_details.user_id as id','beautician_details.business_name')
                            ->groupBy('beautician_id')->get();
          
          static::$data['premiumBeauticians'] = $premiumBeauticians;

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }


    /**
     * function is used to get beauty pro revenue list
     * @return type
     */
    public static function getBeauticianRevenue($month = '', $year = '', $suburb = '') {
        $userModel = new User();
        $data = array();
        $search = '';
        $input = Input::all();

        if (isset($input['search']['value'])) {
            $search = $input['search']['value'];
        }
        if (!empty($input['suburb'])) {
            $suburb = $input['suburb'];
        }
        if (!empty($input['month'])) {
            $month = $input['month'];
        }
        if (!empty($input['year'])) {
            $year = $input['year'];
        }
        if (!$search) {
            $results = $userModel->getBeauticianRevenueListWeb(array('limit' => $input['length'], 'offset' => $input['start'], 'month' => $month, 'year' => $year, 'suburb' => $suburb));
        } else {
            $results = $userModel->getBeauticianRevenueListWeb(array('q' => $search, 'limit' => $input['length'], 'offset' => $input['start'], 'month' => $month, 'year' => $year, 'suburb' => $suburb));
        }

        foreach ($results['result'] as $result) {
            $data[] = array(
                $result->id,
                $result->email,
                ucwords(trim($result->first_name." ".$result->last_name)),
                $result->suburb,
                $result->total_cost
            );
        }

        return array('data' => $data, 'recordsTotal' => $results['count'], "recordsFiltered" => $results['count']);
    }
}
