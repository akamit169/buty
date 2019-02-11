<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: UserServiceProvider.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Providers;

use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Auth;
use App\Utilities\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\BeauticianDetail;
use App\Models\CustomerDetail;
use App\Models\FlaggedUser;
use Log;
use Intervention\Image\Facades\Image as Intervention;
use App\Utilities\FileUpload;
use \App\Models\UserDevice;
use \App\Models\BeauticianAvailabilitySchedule;
use \App\Models\FlagReason;
use \App\Models\RatingReason;
use \App\Models\CustomerBooking;
use \App\Models\BookingRating;

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

            if (!\Hash::check($data['oldPassword'], $user->password)) {
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
     * Send TemporaryPassword
     * 
     * @param type $data
     * @return type
     */
    public static function temporaryPassword($data) {
        try {

            $user = new User();
            $user = User::where('email', '=', $data['email'])->where('user_type', '=', $data['user_type'])->first();
            if ($user) {

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
                static::$data['message'] = trans('messages.user_not_exist');
            }
        } catch (\Exception $e) {

            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * Login Admin User
     * 
     * @param type $data
     * @return type
     */
    public static function loginUser($data, $rememberMe = false) {
        try {

            $user = new User();
            $user = User::where('email', '=', $data['email'])
                            ->where('user_type', '=', $data['user_type'])->first();

            if ($user && \Hash::check($data['password'], $user->password)) {

                if ($user->status == User::IS_INACTIVE) {
                    static::$data['message'] = trans('messages.account_suspended');
                    static::$data['success'] = false;
                } else {
                    Auth::loginUsingId($user->id, $rememberMe);
                    $user->save();
                    static::$data['success'] = true;
                    static::$data['message'] = trans('messages.login_success');
                }
            } else {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.invalid_login_credentials');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to register customer
     * @param array $data
     * @return json
     */
    public static function registerCustomer($data) {
        try {
            $user = false;
            $isSocialSignup = false;
            $referralCodeSharer = new \StdClass;

            $user = User::where('email', $data['email'])->first();

            if ($user && empty($data['facebookId'])) {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.customer.already_exist');
                return static::$data;
            }
            if (!empty($data['facebookId']) && !$user) {
                $user = User::where('fb_id', '=', $data['facebookId'])->first();
            }
            if ($user) {

                //update the respective field of the user
                if (isset($data['facebookId'])) {


                    $userData = ['fb_id' => $data['facebookId'], 'email' => $data['email'], 'first_name' => $data['firstName'], 'last_name' => $data['lastName']];
                } else {
                    $userData = ['email' => $data['email'], 'first_name' => $data['firstName'], 'last_name' => $data['lastName']];
                }
                if ($user->status == User::IS_INACTIVE) {
                    static::$data['success'] = false;
                    static::$data['message'] = trans('messages.account_suspended');
                } else {
                    $status = $user::where('id', $user->id)->update($userData);
                    if ($status) {
                        static::$data['success'] = true;
                        static::$data['message'] = trans('messages.login_success');
                    } else {
                        static::$data['success'] = false;
                        static::$data['message'] = trans('messages.customer.registration_failure');
                    }
                }
            } else {

                if (!empty($data['facebookId'])) {
                    $isSocialSignup = true;
                }

                $referralCode = '';
                if (isset($data['referralCode']) && !empty($data['referralCode'])) {
                    $referralCode = $data['referralCode'];

                    $referralCodeSharer = User::where('referral_code', '=', $referralCode)->first();
                    if (!$referralCodeSharer) {
                        static::$data['message'] = trans('messages.invalid_referral_code');
                        static::$data['success'] = false;
                        static::$data['status_code'] = \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST;

                        return static::$data;
                    }
            
                }
                //create new customer's data
                $user = new User();
                $user->email = $data['email'];
                $user->password = (!empty($data['password']) ? \Hash::make($data['password']) : '');
                $user->user_type = User::IS_CUSTOMER;
                $user->fb_id = (!empty($data['facebookId']) ? $data['facebookId'] : '');
                $user->first_name = $data['firstName'];
                $user->last_name = $data['lastName'];
                $user->referral_code = substr($data['firstName'], 0, 4) . mt_rand(1000, 9999);
                $user->referral_code_used = $referralCode;

                $status = $user->save();
            }
            if ($status) {

                if (!empty($referralCode)) {
                    static::sendReferralCodeUsedEmail($user, $referralCodeSharer);
                    $successMessage = trans('messages.customer.registration_success_with_referral');
                } else {
                    $successMessage = trans('messages.customer.registration_success');
                }

                //login using user id
                $loginToken = static::appUserLogin($data);

                $user = Auth::user();
                if ($user->profile_pic) {
                    $user->profile_pic = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('USER_PROFILE_PIC_S3') . $user->profile_pic;
                }


                if ($loginToken) { //if login token exist then return it with success status
                    $skinColorBasePath = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('SKIN_COLORS_S3');
                    $customerDetails = CustomerDetail::where('user_id', '=', $user->id)
                            ->leftJoin('skin_colors', 'skin_colors.id', '=', 'customer_details.skin_color_id')
                            ->leftJoin('skin_types', 'skin_types.id', '=', 'customer_details.skin_type_id')
                            ->leftJoin('hair_types', 'hair_types.id', '=', 'customer_details.hair_type_id')
                            ->leftJoin('hairlength_types', 'hairlength_types.id', '=', 'customer_details.hairlength_type_id')
                            ->select('customer_details.*', DB::raw("CONCAT('$skinColorBasePath',skin_colors.image) as skinColorImage"), 'skin_types.type as skinType', 'hair_types.type as hairType', 'hairlength_types.type as hairlengthType')
                            ->first();


                    $user['customer_details'] = $customerDetails;

                    static::$data['success'] = true;
                    static::$data['data'] = $user;
                    static::$data['is_social_signup'] = $isSocialSignup;
                    static::$data['accessToken'] = $loginToken;
                    static::$data['message'] = $successMessage;
                } else {
                    static::$data['success'] = false;
                    static::$data['accessToken'] = '';
                    static::$data['message'] = trans('messages.customer.login_failure');
                }
            } else {
                static::$data['success'] = false;
                static::$data['accessToken'] = '';
                if (empty(static::$data['message'])) {
                    static::$data['message'] = trans('messages.customer.registration_failure');
                }
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to save referral code shared by some other user
     * @return type
     */
    public static function saveReferralCode($data) {
        try {

            $referralCodeSharer = User::where('referral_code', '=', $data['referralCode'])->first();
            if (!$referralCodeSharer) {
                static::$data['message'] = trans('messages.invalid_referral_code');
                static::$data['success'] = false;
            } else {
                $user = \Auth::user();
                $user->referral_code_used = $data['referralCode'];
                $user->save();

                static::sendReferralCodeUsedEmail($user, $referralCodeSharer);
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to send email to admin informing that a new user signed up using a referral code
     * @param object $user, $referralCodeSharer
     */
    public static function sendReferralCodeUsedEmail($user, $referralCodeSharer) {
        $admin = User::where('user_type', '=', User::IS_ADMIN)->first();
        $adminEmail = $admin->email;
        $adminName = $admin->first_name;

        $subject = trans('messages.referral_code_used_subject');
        \Mail::send('email.admin.referral_code', ['adminName' => $adminName, 'user' => $user, 'beautician' => $referralCodeSharer], function($message) use ($adminEmail, $subject) {
            $message->to($adminEmail)->subject($subject);
            $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
        });
    }

    /**
     * function is used to log-in app user
     * @param array $arrData
     */
    public static function appUserLogin($arrData) {
        $status = false;
        $token = $facebookId = $password = '';

        $userObj = User::where('email', $arrData['email']);
        if (isset($arrData['userType'])) {
            $userObj->where('user_type', $arrData['userType']);
        }


        $userObj = $userObj->first();

        $facebookId = (isset($arrData['facebookId']) ? $arrData['facebookId'] : '');
        $password = (isset($arrData['password']) ? $arrData['password'] : '');
        if (($userObj && \Hash::check($password, $userObj->password)) || ($userObj && !empty($facebookId) && $facebookId == $userObj->fb_id)) {
            $userDeviceObj = UserDevice::where('user_id', $userObj->id)->first();
            $token = md5(uniqid(mt_rand(), true)) . time();

            if ($userDeviceObj) {
                $userDeviceObj->access_token = $token;
                $userDeviceObj->device_token = $arrData['deviceToken'];
                $userDeviceObj->device_type = $arrData['deviceType'];
                $status = $userDeviceObj->save();
            } else {
                //log-in newly created user by generating user token
                $userDeviceObj = new UserDevice();
                $userDeviceObj->user_id = $userObj->id;
                $userDeviceObj->access_token = $token;
                $userDeviceObj->device_token = $arrData['deviceToken'];
                $userDeviceObj->device_type = $arrData['deviceType'];
                $status = $userDeviceObj->save();
            }
        }
        if ($status) {
            Auth::loginUsingId($userObj->id);
            return $token;
        } else {
            return $status;
        }
    }

    /**
     * function is used to login app user
     * @param array $data
     */
    public static function loginAppUser($data) {
        try {
            $loginToken = false;
            $loginToken = static::appUserLogin($data);
            if ($loginToken) {
                $userObj = Auth::user();

                if ($userObj->status == User::IS_INACTIVE ||
                        ($userObj->admin_approval_status != User::IS_APPROVED && $userObj->user_type == User::IS_BEAUTICIAN)) {
                    //delete from user device table
                    UserDevice::where('user_id', $userObj->id)->delete();
                    static::$data['success'] = false;
                    static::$data['accessToken'] = '';
                    if ($userObj->status == User::IS_INACTIVE) {
                        static::$data['message'] = trans('messages.account_suspended');
                    } else if ($userObj->admin_approval_status == User::IS_DISAPPROVED) {
                        static::$data['message'] = trans('messages.registration_denied');
                    } else {
                        static::$data['message'] = trans('messages.approval_awaits');
                    }

                    static::$data['data'] = $userObj;
                } else {

                    if ($userObj->user_type == User::IS_BEAUTICIAN) {
                        static::$data['data'] = static::getBeauticianDetails();
                    } else {
                        static::$data['data'] = static::getCustomerDetails();
                    }

                    static::$data['success'] = true;

                    static::$data['accessToken'] = $loginToken;
                    static::$data['message'] = trans('messages.login_successful');
                }
            } else {
                static::$data['success'] = false;
                static::$data['accessToken'] = '';
                static::$data['message'] = trans('messages.invalid_credentials');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }


     /**
     * function is used to get user details
     * @param array $data
     */
    public static function getUserDetails() {
        try {
              $userObj = Auth::user();

              if ($userObj->user_type == User::IS_BEAUTICIAN) {
                static::$data['data'] = static::getBeauticianDetails();
              } else {
                static::$data['data'] = static::getCustomerDetails();
              }

                
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /* get customer details */

    public static function getCustomerDetails($customerId = false) {

        if ($customerId) {
            $user = User::find($customerId);
        } else {
            $user = \Auth::user();
        }


        if ($user->profile_pic) {
            $user->profile_pic = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('USER_PROFILE_PIC_S3') . $user->profile_pic;
        }

        $skinColorBasePath = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('SKIN_COLORS_S3');

        $customerDetails = CustomerDetail::where('user_id', '=', $user->id)
                ->leftJoin('skin_colors', 'skin_colors.id', '=', 'customer_details.skin_color_id')
                ->leftJoin('skin_types', 'skin_types.id', '=', 'customer_details.skin_type_id')
                ->leftJoin('hair_types', 'hair_types.id', '=', 'customer_details.hair_type_id')
                ->leftJoin('hairlength_types', 'hairlength_types.id', '=', 'customer_details.hairlength_type_id')
                ->select('customer_details.*', DB::raw("CONCAT('$skinColorBasePath',skin_colors.image) as skinColorImage"), 'skin_types.type as skinType', 'hair_types.type as hairType', 'hairlength_types.type as hairlengthType')
                ->first();


        //fetch user pending rating list
        if (!empty($customerDetails)) {
            $customerDetails->pendingRating = static::getPendingRating();
        }
        $user['customer_details'] = $customerDetails;

        return $user;
    }

    /* get beautician details */

    public static function getBeauticianDetails() {
        $user = \Auth::user();
        if ($user->profile_pic) {
            $user->profile_pic = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('USER_PROFILE_PIC_S3') . $user->profile_pic;
        }
        $beauticianDetails = BeauticianDetail::where('user_id', '=', $user->id)->first();
        $currentWeekAvailabilityFlag = static::getActivitySet();
        $beauticianDetails->currentWeekAvailabilityFlag = $currentWeekAvailabilityFlag;
        $beauticianDetails->travelCost = DB::table('admin_settings')
                        ->where('config_key', '=', 'travel_cost')->pluck('config_value')->first();
        //fetch user pending rating list
        $beauticianDetails->pendingRating = static::getPendingRating();
        $user['beautician_details'] = $beauticianDetails;
        return $user;
    }

    /**
     * function is used to check if beautician is available for current week or not 
     * @return INT $currentWeekAvailabilityFlag | 0 => 'Unavailable', 1 => 'Available'
     */
    public static function getActivitySet() {
        $user = \Auth::user();

        $getCurrentWeekDate = static::getXWeekRange(date('Y-m-d'));

        $startDate = $getCurrentWeekDate['start_date'];
        $endDate = $getCurrentWeekDate['end_date'];
        $currentWeekAvailabilityFlag = 0;

        $scheduleObj =   BeauticianAvailabilitySchedule::where('beautician_id', $user->id)
                        ->where('start_datetime', '>=', $startDate)
                        ->where('end_datetime', '<=', $endDate)->first();
        if (!empty($scheduleObj)) {
            $currentWeekAvailabilityFlag = 1;
        }
        return $currentWeekAvailabilityFlag;
    }

    /**
     * function is used to get current week start and end date
     * @param date $date | current date in Y-m-d format
     * @return array
     */
    public static function getXWeekRange($date) {
        $ts = strtotime($date);
        $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
        return array('start_date' => date('Y-m-d', $start), 'end_date' => date('Y-m-d', strtotime('next saturday', $start)));
    }

    /**
     * function is used to logout app user
     * @return type
     */
    public static function logoutAppUser() {
        try {
            $userObj = \Auth::user();
            //logging out user by deleting its object from device token
            $status = UserDevice::where('user_id', $userObj->id)->delete();
            if ($status) {
                static::$data['success'] = true;
                static::$data['message'] = trans('messages.logout');
            } else {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.logout');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used send forgot password email
     * @return type
     */
    public static function forgotPassword($data) {
        try {

            $user = User::where('email', '=', $data['email'])->first();

            if ($user) {
                $token = md5(uniqid(mt_rand(), true)) . time();

                $res = PasswordReset::where('email', '=', $data['email'])->first();
                if ($res) {
                    PasswordReset::where('email', '=', $data['email'])->update(['token' => $token]);
                } else {
                    PasswordReset::insert(['email' => $data['email'], 'token' => $token]);
                }


                $linkPrefix = $user->user_type == User::IS_CUSTOMER ? 'customer' : 'beautician';

                //send email
                $userEmail = $user->email;
                $username = ucfirst($user->first_name) . " " . ucfirst($user->last_name);
                $link = url($linkPrefix . '/resetPassword') . '?token=' . $token;

                $subject = trans('messages.password_reset_subject');
                \Mail::send('email.user.forgot_password', ['link' => $link, 'username' => $username], function($message) use ($userEmail, $subject) {
                    $message->to($userEmail)->subject($subject);
                    $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                });

                static::$data['message'] = trans('messages.forgot_pswd_email');
            } else {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.incorrect_email');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to register beautician to the site and login user
     * @param array $data
     * @return type
     */
    public static function registerBeautician($data) {
        try {
            if (!$data['certificateFileName']) {
                static::$data['success'] = false;
                static::$data['data'] = '';
                static::$data['accessToken'] = '';
                static::$data['message'] = trans('messages.image_upload_failure');
            } else {
                $beauticianStatus = $status = false;


                DB::beginTransaction();
                $user = new User();
                $user->email = $data['email'];
                $user->password = (!empty($data['password']) ? \Hash::make($data['password']) : '');
                $user->user_type = User::IS_BEAUTICIAN;
                $user->first_name = $data['firstName'];
                $user->last_name = $data['lastName'];
                $user->phone_number = $data['phone'];
                $status = $user->save();
                if ($status) {
                    $beautician = new BeauticianDetail();
                    $beautician->user_id = $user->id;
                    $beautician->abn = $data['abn'];
                    $beautician->business_name = $data['businessName'];
                    $beautician->instagram_link = isset($data['instaId']) ? $data['instaId'] : "";
                    $beautician->police_check_certificate = $data['certificateFileName'];
                    $beauticianStatus = $beautician->save();
                    if ($beauticianStatus) {
                        static::$data['success'] = true;
                        static::$data['data'] = $user;
                        static::$data['redirectUrl'] = url(('beautician/signup-success'));
                        static::$data['message'] = trans('messages.beautician.registration_success');
                    } else {
                        static::$data['success'] = false;
                        static::$data['message'] = trans('messages.customer.registration_failure');
                    }
                } else {
                    static::$data['success'] = false;
                    static::$data['message'] = trans('messages.customer.registration_failure');
                }
                DB::commit();
                if ($beauticianStatus) {
                    //send email to beautician as well as to admin regarding beautician sign up
                    $notificationData = array('userId' => $user->id);

                    $url = url('api/beautician/sendRegisteredEmail');
                    exec('curl -H "Accept: application/json"  -H "Content-type: application/json" -X POST -d ' . "'" . json_encode($notificationData) . "'" . ' ' . $url . '  > /dev/null &');
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * get User By Reset Password Token
     * @return type
     */
    public static function getUserByResetPasswordToken($data) {
        try {

            $token = PasswordReset::where('token', '=', $data['token'])->first();
            if (!$token) {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.invalid_token');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * reset forgotten password
     * @return type
     */
    public static function resetPassword($data) {
        try {

            $token = PasswordReset::where('token', '=', $data['token'])->first();
            if ($token) {
                $token->delete();
                $password = \Hash::make($data['password']);
                User::where('email', '=', $token->email)
                        ->update(['password' => $password]);

                static::$data['message'] = trans('messages.password_changed');
            } else {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.incorrect_email');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * upload user profile pic
     * @return type
     */
    public static function postUserProfilePic($imageFileObject) {
        try {


            $filePath = env('USER_PROFILE_PIC_S3');

            $fileUpload = new FileUpload();
            $imageFileName = $fileUpload->uploadFileToS3($imageFileObject, $filePath);
            if ($imageFileName) {
                $user = Auth::user();

                if ($user->profile_pic) {
                    \Storage::delete(env('USER_PROFILE_PIC_S3') . $user->profile_pic);
                }

                $user->profile_pic = $imageFileName;
                $user->save();

                static::$data['message'] = trans('messages.image_uploaded');
                static::$data['profilePic'] = env('S3_BUCKET_PATH') .
                        env('S3_BUCKET') . env('USER_PROFILE_PIC_S3') . $imageFileName;
            } else {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.image_upload_server_error');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * set up customer profile
     * @return type
     */
    public static function setupCustomerProfile($data) {
        try {


            DB::beginTransaction();

            $user = Auth::user();
            $user->address = isset($data['address']) ? $data['address'] : '';
            $user->suburb = $data['suburb'];
            $user->state = $data['state'];
            $user->country = $data['country'];
            $user->lat = $data['lat'];
            $user->lng = $data['lng'];
            $user->gender = $data['gender'];
            $user->date_of_birth = $data['dateOfBirth'];
            $user->phone_number = $data['phone'];
            $user->zipcode = $data['postalCode'];
            $user->save();

            $customerDetails = CustomerDetail::where('user_id', '=', $user->id)->first();
            if (!$customerDetails) {
                $customerDetails = new CustomerDetail();
            }

            $customerDetails->user_id = $user->id;
            $customerDetails->skin_color_id = $data['skinColorId'];
            $customerDetails->skin_type_id = $data['skinTypeId'];
            $customerDetails->hair_type_id = $data['hairTypeId'];
            $customerDetails->hairlength_type_id = $data['hairlengthTypeId'];
            $customerDetails->is_hair_colored = $data['isHairColored'];
            $customerDetails->allergies = isset($data['allergies']) ? $data['allergies'] : '';
            $customerDetails->description = $data['description'];
            $customerDetails->save();

            static::$data['message'] = trans('messages.update_successful');
            static::$data['user'] = static::getCustomerDetails();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to upload file to s3 storage
     * @param object $imageFileObject
     * @return If true imagefile name $imageFileName Or boolean $status as false 
     */
    public static function uploadFileToS3($imageFileObject) { 
        $status = false; 
        try {
            $img = Intervention::make($imageFileObject);
            $img->orientate();
            $img->save();
            $imageFileName = time() . '.' . $imageFileObject->getClientOriginalExtension();
            $s3 = \Storage::disk('s3');
            $filePath = env('REPORT_IMAGES_S3') . $imageFileName;
            $status = $s3->put($filePath, file_get_contents($imageFileObject), 'public');

            if ($status) {
                return $imageFileName;
            } else {
                return $status;
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
            return $status;
        }
    }

    /**
     * function is used to send new beautician registration notification to admin
     */
    public static function sendRegisteredEmail($data) {
        Log::error($data);
        $userId = $data['userId'];
        $userObj = User::where('id', $userId)->first();
        if ($userObj) {
            //send successful registration email to beautician and admin
            $subject = trans('messages.beautician.registeration_success');

            \Mail::send('email.beautician.register_notification', ['userObj' => $userObj], function($message) use ($userObj, $subject) {
                $message->to($userObj->email)->subject($subject);
                $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
            });
            if (count(\Mail::failures()) == 0) {
                Log::error('mail for newly beautician registered with id: ' . $userId . ' has been sent successfully');
                //send email to admin
                $subject = trans('messages.beautician.registeration_success_reminder_to_admin');
                $subject .= ' ' . ucfirst($userObj->first_name) . ' ' . ucfirst($userObj->last_name);
                \Mail::send('email.beautician.admin_register_notification', ['userObj' => $userObj], function($message) use ($subject) {
                    $message->to(env('ADMIN_EMAIL'))->subject($subject);
                    $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                });
                if (count(Mail::failures()) == 0) {
                    Log::error('mail for newly beautician registered with id: ' . $userId . ' has been sent to admin successfully');
                } else {
                    Log::error('mail for newly beautician registered with id: ' . $userId . ' can not be sent to admin.');
                }
            } else {
                Log::error('mail for newly beautician registered with id: ' . $userId . ' can not be sent.');
            }
        } else {
            Log::error('No user found to send registered email to id: ' . $userId);
        }
    }

    /**
     * function is used to delete user who has been suspended for more than a predefined time
     */
    public static function deleteSuspendedUser() {
        static::$data['success'] = false;
        try {
            $deleteTimeForSuspendedUser = env('DELETE_TIME_FOR_SUSPENDED_USER', 1);
            static::$data['success'] = User::whereRaw('profile_declined_at <= now() + INTERVAL -' . $deleteTimeForSuspendedUser . ' DAY')
                            ->where('profile_declined_at', '!=', '0000-00-00 00:00:00')
                            ->where('admin_approval_status', User::IS_DISAPPROVED)->delete();
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to flag a user
     * @param  $data
     * @return type
     */
    public static function flagUser($data) {
        try {
            $user = \Auth::user();
            $userId = $user->id;
            $alreadyFlagged = FlaggedUser::where('flagged_by', $userId)->where('flagged_user', $data['flaggedUser'])->where('flag_reason_id', $data['reasonId'])->first();

            if ($alreadyFlagged) {
                static::$data['message'] = trans('messages.already_flagged');
                static::$data['success'] = false;
            } else {
                FlaggedUser::insert(['flagged_by' => $userId, 'flagged_user' => $data['flaggedUser'], 'flag_reason_id' => $data['reasonId']]);

                User::where('id', $data['flaggedUser'])->update(['is_flagged' => User::IS_FLAGGED]);

                $reason = FlagReason::where('id', '=', $data['reasonId'])->first()->reason;

                //send email to admin

                $flaggedBy = [
                    'name' => trim($user->first_name . ' ' . $user->last_name),
                    'userType' => $user->user_type == User::IS_BEAUTICIAN ? 'Beauty Pro' : 'Customer',
                    'email' => $user->email
                ];


                $flaggedUserObj = User::find($data['flaggedUser']);
                $flaggedUser = [
                    'name' => trim($flaggedUserObj->first_name . ' ' . $flaggedUserObj->last_name),
                    'userType' => $flaggedUserObj->user_type == User::IS_BEAUTICIAN ? 'Beauty Pro' : 'Customer',
                    'email' => $flaggedUserObj->email
                ];

                $admin = User::where('user_type', '=', User::IS_ADMIN)->first();
                $adminEmail = $admin->email;
                $adminName = $admin->first_name;

                $subject = trans('messages.user.user_flagged_subject');
                \Mail::send('email.user.user_flagged', ['adminName' => $adminName, 'reason' => $reason, 'flaggedBy' => $flaggedBy, 'flaggedUser' => $flaggedUser], function($message) use ($adminEmail, $subject) {
                    $message->to($adminEmail)->subject($subject);
                    $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                });

                static::$data['message'] = trans('messages.user_flagged');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

   /**
     * function is used to get available flag reasons
     * @param  $data
     * @return type
     */
    public static function getFlagReasons($userType)
    {
     try {
            $userType = $userType == User::IS_BEAUTICIAN?User::IS_CUSTOMER:User::IS_BEAUTICIAN;
            $reasons = FlagReason::where('user_type',$userType)->get();
            static::$data['reasons'] = $reasons;

        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    } 

    /**
     * function is used to fetch rating reason
     * @return type
     */
    public static function fetchRatingReason() {
        try {
            static::$data['success'] = false;
            $type = '';
            $user = \Auth::user();
            if ($user->user_type == User::IS_BEAUTICIAN) {
                $type = RatingReason::IS_CUSTOMER_TYPE;
            } else {
                $type = RatingReason::IS_BEAUTICIAN_TYPE;
            }
            $ratingReason = RatingReason::where('type', $type)->get()->toArray();
            if (count($ratingReason) > 0) {
                static::$data['success'] = true;
                static::$data['reason'] = $ratingReason;
                static::$data['message'] = trans('messages.customer_booking.rating_reason_fetched_success');
            } else {
                static::$data['reason'] = $ratingReason;
                static::$data['message'] = trans('messages.customer_booking.rating_reason_fetched_failure');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to fetch user's previos rating
     * @return type
     */
    public static function fetchUserPreviousRating($arrData = array(), $webCall = false) {
        try {
            if (count($arrData) == 0) {
                $user = \Auth::user();
                $userId = $user->id;
            } else {
                $userId = $arrData['userId'];
            }
            $profilePicPath = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('USER_PROFILE_PIC_S3');
            $userRating = BookingRating::join('users', 'users.id', '=', 'booking_ratings.rated_by')
                    ->leftjoin('rating_reasons', 'rating_reasons.id', '=', 'booking_ratings.below_rating_reason')
                    ->leftjoin('beautician_details', 'beautician_details.user_id', '=', 'users.id')
                    ->select('booking_ratings.rating', 'users.first_name', 'users.last_name', 'booking_ratings.comment', 'beautician_details.business_name', DB::raw('IF(rating_reasons.id IS NULL, "", rating_reasons.reason) as rating_reason'), DB::raw('IF(users.profile_pic = "", "", CONCAT("' . $profilePicPath . '", users.profile_pic)) as profile_pic'))
                    ->where('booking_ratings.rated_to', $userId)
                    ->orderBy('booking_ratings.id', 'desc');
            if ($webCall) {
                $result = $userRating->get()->toArray();
            } else {
                $result = $userRating->paginate(\Config::get('constants.PER_PAGE'))->toArray();
            }
            if (!empty($result)) {
                static::$data['success'] = true;
            } else {
                static::$data['success'] = false;
            }
            static::$data['data'] = $result;
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }


    /**
     * function is used to get States in Australia
     * @return type
     */
    public static function getStates() {
        try {
           static::$data['states'] = config('constants.AUS_STATES');
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to get list of customer booking id whose rating are pending
     * @param int $userId
     * @return array
     */
    public static function getPendingRating($userId = '', $userType = '') {
        $arrPendingRating = [];
        try {
            if (empty($userId)) {
                $userId = \Auth::user()->id;
                $userType = \Auth::user()->user_type;
            }
            $arrPendingRating = CustomerBooking::leftjoin('booking_ratings', function($query) use($userId) {
                        $query->on('booking_ratings.customer_booking_id', '=', 'customer_bookings.id')
                        ->where('booking_ratings.rated_by', '=', $userId);
                    })
                    ->where('customer_bookings.status', CustomerBooking::IS_DONE_PAYMENT_LEFT);
            if ($userType == User::IS_BEAUTICIAN) {
                $arrPendingRating->where('customer_bookings.beautician_id', $userId);
            } else {
                $arrPendingRating->where('customer_bookings.customer_id', $userId);
            }
            $arrPendingRating = $arrPendingRating->select('customer_bookings.id as bookingId')
                            ->get()->toArray();
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return $arrPendingRating;
    }

    /**
     * 
     * @return type
     */
    public static function getBeauticianServiceImages($id) {
        try {
            $serviceImage = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('SERVICE_IMAGE_FOLDER');
            $userImage = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('USER_PROFILE_PIC_S3');
            $beauticianServices = User::join('beautician_portfolios', 'beautician_portfolios.user_id', '=', 'users.id')
                            ->join('beautician_details','beautician_details.user_id','=','users.id')
                            ->select('beautician_details.business_name','beautician_portfolios.created_at as created_date', DB::raw('IF(users.profile_pic IS NULL, "", CONCAT("' . $userImage . '",users.profile_pic)) as profile_image'), DB::raw('IF(beautician_portfolios.image IS NULL, "", CONCAT("' . $serviceImage . '",beautician_portfolios.image)) as service_image'))
                            ->where('beautician_portfolios.service_id', $id)
                            ->inRandomOrder()->take(12)->get()->toArray();
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return $beauticianServices;
    }

    
    /**
     * function is used to validate if user has already rated for booking or not
     * @param type $bookingId
     * @return type
     */
    public static function validateRating($bookingId) {
        $ratingObj = '';
        try {
            $userId = \Auth::user()->id;
            $ratingObj = BookingRating::where('customer_booking_id', $bookingId)->where('rated_by', $userId)->first();
            
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return $ratingObj;
    }
    
    /**
     * function is used to upload temp image into public directory
     * @param type $request
     * @return type
     */
    public static function uploadFileToTemp($request) {
        $image = $request->file('file');
        $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
        $userId = \Auth::user()->id;
        if (!file_exists(public_path('temp_images'))) {
            mkdir(public_path('temp_images'), 0777, true);
        }
        $destinationPath = public_path('temp_images/'.$userId);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        return $image->move($destinationPath, $input['imagename']);
    }
    
    /**
     * function is used to get beautician monthly report
     * @return type
     */
    public static function getBeauticianMonthlyReport() {
        $currentMonth = date('m'); $currentYear = date('Y');
        if($currentMonth == 1) {
            $currentMonth = 12;
            $currentYear = $currentYear - 1;
        } else {
            $currentMonth = $currentMonth - 1;
        }
        
        $arrCompletedJobs = User::getTotalCompletedJobs($currentMonth, $currentYear);
        $arrCancelledJobs = User::getTotalCancelledJobs($currentMonth, $currentYear);
        $arrDisputedJobs = User::getTotalDisputedJobs($currentMonth, $currentYear);
        $arrTotalRevenue = User::getTotalRevenue($currentMonth, $currentYear);
        $arrBeautician = User::where('user_type', User::IS_BEAUTICIAN)->where('status', User::IS_ACTIVE)
                            ->select('id', 'email')->get()->toArray();
        
        $arrBeauticianMonthlyReport = static::mapBeauticianMonthlyReport($arrBeautician, $arrCompletedJobs, $arrCancelledJobs, $arrDisputedJobs, $arrTotalRevenue);
        return $arrBeauticianMonthlyReport;
    }
    
    /**
     * function is used to map all the records to individual beautician
     * @param array $arrBeautician
     * @param array $arrCompletedJobs
     * @param array $arrCancelledJobs
     * @param array $arrDisputedJobs
     * @param array $arrTotalRevenue
     * @return array
     */
    public static function mapBeauticianMonthlyReport($arrBeautician, $arrCompletedJobs, $arrCancelledJobs, $arrDisputedJobs, $arrTotalRevenue) {

        if(count($arrBeautician)>0) {
            foreach($arrBeautician as $key=>$beauticianId) {
                $arrBeautician[$key]['total_completed_jobs'] = 0;
                $arrBeautician[$key]['total_cancelled_jobs'] = 0;
                $arrBeautician[$key]['total_disputed_jobs'] = 0;
                $arrBeautician[$key]['total_cost'] = 0;
                //map completed jobs to beautician
                foreach($arrCompletedJobs as $ckey=>$cvalue) {
                    if($cvalue->id == $beauticianId['id']) {
                        $arrBeautician[$key]['total_completed_jobs'] = $cvalue->total_completed_jobs;
                        unset($arrCompletedJobs[$ckey]);
                    }
                }
                //map cancelled jobs to beautician
                foreach($arrCancelledJobs as $cankey=>$canvalue) {
                    if($canvalue->id == $beauticianId['id']) {
                        $arrBeautician[$key]['total_cancelled_jobs'] = $canvalue->total_cancelled_jobs;
                        unset($arrCancelledJobs[$cankey]);
                    }
                }
                //map disputed jobs to beautician
                foreach($arrDisputedJobs as $dkey=>$dvalue) {
                    if($dvalue->id == $beauticianId['id']) {
                        $arrBeautician[$key]['total_disputed_jobs'] = $dvalue->total_disputed_jobs;
                        unset($arrDisputedJobs[$dkey]);
                    }
                }
                //map revenue generated to beautician
                foreach($arrTotalRevenue as $rkey=>$rvalue) {
                    if($rvalue->id == $beauticianId['id']) {
                        $arrBeautician[$key]['total_cost'] = $rvalue->total_cost;
                        unset($arrTotalRevenue[$rkey]);
                    }
                }
            }
        }
        return $arrBeautician;
    }
    
    /**
     * function is used to generate beautician report graph
     * @param int $id
     */
    public static function generateBeauticianCompletedServiceGraph($id) {
        $currentMonth = date('m'); $currentYear = date('Y');
        if($currentMonth == 1) {
            $currentMonth = 12;
            $currentYear = $currentYear - 1;
        } else {
            $currentMonth = $currentMonth - 1;
        }
        
        return User::getTotalCompletedServices($id, $currentMonth, $currentYear);
    }
    
    /**
     * function is used to generate beautician average rating
     * @param int $id
     * @return type
     */
    public static function generateBeauticianRatingGraph($id) {
        $currentYear = date('Y'); $currentMonth = date('m'); 
        if($currentMonth == 1) {
            $currentMonth = 12;
            $currentYear = $currentYear - 1;
        }
        return  User::getRatingPerMonth($id, $currentYear);
    }


    public static function updateHairSubCat(){
        echo __FUNCTION__ ; echo "\n";
       $parentId = DB::table("services")->where("name","Hair")->first()->id; 


       $updatedHairCatNames = DB::table("updated_hair")->pluck('service')->all(); 

       $updatedHairCatNames = array_map('trim',$updatedHairCatNames); 
        
       DB::statement("set foreign_key_checks=0");
       DB::table('services')->where('parent_id',$parentId)->whereNotIn('name',$updatedHairCatNames)->get();

       $updatedHairCats = DB::table("updated_hair")->get();
       foreach ($updatedHairCats as $updatedHairCat) {  
        $serviceName = trim($updatedHairCat->service);
         $service = DB::table("services")->where('services.name','=',$serviceName)->first(); 
         if($service)
         {
            DB::table("services")->where('services.name','=',$serviceName)->update(['description' => $updatedHairCat->description, 'tip' => $updatedHairCat->tips]);
         }
         else
         {
            DB::table("services")->insert(['name' => $serviceName,'description' => $updatedHairCat->description, 'tip' => $updatedHairCat->tips,'parent_id' => $parentId]);
         }

       }

    }


    public static function updateAestheticsSubCat(){
        echo __FUNCTION__ ; echo "\n";
       $parentId = DB::table("services")->where("name","Aesthetics")->first()->id; 


       $updatedAestheticsCatNames = DB::table("updated_aesthetics")->pluck('service')->all(); 

       $updatedAestheticsCatNames = array_map('trim',$updatedAestheticsCatNames); 
        
       DB::statement("set foreign_key_checks=0");
       DB::table('services')->where('parent_id',$parentId)->whereNotIn('name',$updatedAestheticsCatNames)->get();

       $updatedAestheticsCats = DB::table("updated_aesthetics")->get();
       foreach ($updatedAestheticsCats as $updatedAestheticsCat) {  
        $serviceName = trim($updatedAestheticsCat->service);
         $service = DB::table("services")->where('services.name','=',$serviceName)->first(); 
         if($service)
         {
            DB::table("services")->where('services.name','=',$serviceName)->update(['description' => $updatedAestheticsCat->description, 'tip' => $updatedAestheticsCat->tips]);
         }
         else
         {
            DB::table("services")->insert(['name' => $serviceName,'description' => $updatedAestheticsCat->description, 'tip' => $updatedAestheticsCat->tips,'parent_id' => $parentId]);
         }

       }

    }


     public static function updateBarberingSubCat(){
        echo __FUNCTION__ ; echo "\n";
       $parentId = DB::table("services")->where("name","Barbering")->first()->id; 


       $updatedBarberingCatNames = DB::table("updated_barbering")->pluck('service')->all(); 

       $updatedBarberingCatNames = array_map('trim',$updatedBarberingCatNames); 
        
       DB::statement("set foreign_key_checks=0");
       DB::table('services')->where('parent_id',$parentId)->whereNotIn('name',$updatedBarberingCatNames)->get();

       $updatedBarberingCats = DB::table("updated_barbering")->get();
       foreach ($updatedBarberingCats as $updatedBarberingCat) {  
        $serviceName = trim($updatedBarberingCat->service);
         $service = DB::table("services")->where('services.name','=',$serviceName)->first(); 
         if($service)
         {
            DB::table("services")->where('services.name','=',$serviceName)->update(['description' => $updatedBarberingCat->description, 'tip' => $updatedBarberingCat->tips]);
         }
         else
         {
            DB::table("services")->insert(['name' => $serviceName,'description' => $updatedBarberingCat->description, 'tip' => $updatedBarberingCat->tips,'parent_id' => $parentId]);
         }

       }

    }


     public static function updateBrowsSubCat(){
        echo __FUNCTION__ ; echo "\n";
       $parentId = DB::table("services")->where("name","Brows")->first()->id; 


       $updatedBrowsCatNames = DB::table("updated_brows")->pluck('service')->all(); 

       $updatedBrowsCatNames = array_map('trim',$updatedBrowsCatNames); 
        
       DB::statement("set foreign_key_checks=0");
       DB::table('services')->where('parent_id',$parentId)->whereNotIn('name',$updatedBrowsCatNames)->get();

       $updatedBrowsCats = DB::table("updated_brows")->get();
       foreach ($updatedBrowsCats as $updatedBrowsCat) {  
        $serviceName = trim($updatedBrowsCat->service);
         $service = DB::table("services")->where('services.name','=',$serviceName)->first(); 
         if($service)
         {
            DB::table("services")->where('services.name','=',$serviceName)->update(['description' => $updatedBrowsCat->description, 'tip' => $updatedBrowsCat->tips]);
         }
         else
         {
            DB::table("services")->insert(['name' => $serviceName,'description' => $updatedBrowsCat->description, 'tip' => $updatedBrowsCat->tips,'parent_id' => $parentId]);
         }

       }

    }

    public static function updateCosmeticTatooingSubCat(){
        echo __FUNCTION__ ; echo "\n";
       $parentId = DB::table("services")->where("name","Cosmetic tattooing")->first()->id; 


       $updatedcosmeticTatooingCatNames = DB::table("updated_cosmeticTatooing")->pluck('service')->all(); 

       $updatedcosmeticTatooingCatNames = array_map('trim',$updatedcosmeticTatooingCatNames); 
        
       DB::statement("set foreign_key_checks=0");
       DB::table('services')->where('parent_id',$parentId)->whereNotIn('name',$updatedcosmeticTatooingCatNames)->get();

       $updatedcosmeticTatooingCats = DB::table("updated_cosmeticTatooing")->get();
       foreach ($updatedcosmeticTatooingCats as $updatedcosmeticTatooingCat) {  
        $serviceName = trim($updatedcosmeticTatooingCat->service);
         $service = DB::table("services")->where('services.name','=',$serviceName)->first(); 
         if($service)
         {
            DB::table("services")->where('services.name','=',$serviceName)->update(['description' => $updatedcosmeticTatooingCat->description, 'tip' => $updatedcosmeticTatooingCat->tips]);
         }
         else
         {
            DB::table("services")->insert(['name' => $serviceName,'description' => $updatedcosmeticTatooingCat->description, 'tip' => $updatedcosmeticTatooingCat->tips,'parent_id' => $parentId]);
         }

       }

    }

    public static function updateHairMakeupSubCat(){
        echo __FUNCTION__ ; echo "\n";
       $parentId = DB::table("services")->where("name","Hair + Makeup")->first()->id; 


       $updatedHairMakeUpCatNames = DB::table("updated_hair+makeup")->pluck('service')->all(); 

       $updatedHairMakeUpCatNames = array_map('trim',$updatedHairMakeUpCatNames); 
        
       DB::statement("set foreign_key_checks=0");
       DB::table('services')->where('parent_id',$parentId)->whereNotIn('name',$updatedHairMakeUpCatNames)->get();

       $updatedHairMakeUpCats = DB::table("updated_hair+makeup")->get();
       foreach ($updatedHairMakeUpCats as $updatedHairMakeUpCat) {  
        $serviceName = trim($updatedHairMakeUpCat->service);
         $service = DB::table("services")->where('services.name','=',$serviceName)->first(); 
         if($service)
         {
            DB::table("services")->where('services.name','=',$serviceName)->update(['description' => $updatedHairMakeUpCat->description, 'tip' => $updatedHairMakeUpCat->tips]);
         }
         else
         {
            DB::table("services")->insert(['name' => $serviceName,'description' => $updatedHairMakeUpCat->description, 'tip' => $updatedHairMakeUpCat->tips,'parent_id' => $parentId]);
         }

       }

    }


     public static function updateHairRemovalSubCat(){
        echo __FUNCTION__ ; echo "\n";
       $parentId = DB::table("services")->where("name","Hair removal")->first()->id; 


       $updatedHairRemovalCatNames = DB::table("updated_hairRemoval")->pluck('service')->all(); 

       $updatedHairRemovalCatNames = array_map('trim',$updatedHairRemovalCatNames); 
        
       DB::statement("set foreign_key_checks=0");
       DB::table('services')->where('parent_id',$parentId)->whereNotIn('name',$updatedHairRemovalCatNames)->get();

       $updatedHairRemovalCats = DB::table("updated_hairRemoval")->get();
       foreach ($updatedHairRemovalCats as $updatedHairRemovalCat) {  
        $serviceName = trim($updatedHairRemovalCat->service);
         $service = DB::table("services")->where('services.name','=',$serviceName)->first(); 
         if($service)
         {
            DB::table("services")->where('services.name','=',$serviceName)->update(['description' => $updatedHairRemovalCat->description, 'tip' => $updatedHairRemovalCat->tips]);
         }
         else
         {
            DB::table("services")->insert(['name' => $serviceName,'description' => $updatedHairRemovalCat->description, 'tip' => $updatedHairRemovalCat->tips,'parent_id' => $parentId]);
         }

       }

    }


    public static function updateLashesSubCat(){
        echo __FUNCTION__ ; echo "\n";
       $parentId = DB::table("services")->where("name","Lashes")->first()->id; 


       $updatedLashesCatNames = DB::table("updated_lashes")->pluck('service')->all(); 

       $updatedLashesCatNames = array_map('trim',$updatedLashesCatNames); 
        
       DB::statement("set foreign_key_checks=0");
       DB::table('services')->where('parent_id',$parentId)->whereNotIn('name',$updatedLashesCatNames)->get();

       $updatedLashesCats = DB::table("updated_lashes")->get();
       foreach ($updatedLashesCats as $updatedLashesCat) {  
        $serviceName = trim($updatedLashesCat->service);
         $service = DB::table("services")->where('services.name','=',$serviceName)->first(); 
         if($service)
         {
            DB::table("services")->where('services.name','=',$serviceName)->update(['description' => $updatedLashesCat->description, 'tip' => $updatedLashesCat->tips]);
         }
         else
         {
            DB::table("services")->insert(['name' => $serviceName,'description' => $updatedLashesCat->description, 'tip' => $updatedLashesCat->tips,'parent_id' => $parentId]);
         }

       }

    }


     public static function updateMakeUpSubCat(){
        echo __FUNCTION__ ; echo "\n";
       $parentId = DB::table("services")->where("name","Makeup")->first()->id; 


       $updatedMakeUpCatNames = DB::table("updated_makeup")->pluck('service')->all(); 

       $updatedMakeUpCatNames = array_map('trim',$updatedMakeUpCatNames); 
        
       DB::statement("set foreign_key_checks=0");
       DB::table('services')->where('parent_id',$parentId)->whereNotIn('name',$updatedMakeUpCatNames)->get();

       $updatedMakeUpCats = DB::table("updated_makeup")->get();
       foreach ($updatedMakeUpCats as $updatedMakeUpCat) {  
        $serviceName = trim($updatedMakeUpCat->service);
         $service = DB::table("services")->where('services.name','=',$serviceName)->first(); 
         if($service)
         {
            DB::table("services")->where('services.name','=',$serviceName)->update(['description' => $updatedMakeUpCat->description, 'tip' => $updatedMakeUpCat->tips]);
         }
         else
         {
            DB::table("services")->insert(['name' => $serviceName,'description' => $updatedMakeUpCat->description, 'tip' => $updatedMakeUpCat->tips,'parent_id' => $parentId]);
         }

       }

    }


    public static function updateNailsSubCat(){
        echo __FUNCTION__ ; echo "\n";
       $parentId = DB::table("services")->where("name","Nails")->first()->id; 


       $updatedNailsCatNames = DB::table("updated_nails")->pluck('service')->all(); 

       $updatedNailsCatNames = array_map('trim',$updatedNailsCatNames); 
        
       DB::statement("set foreign_key_checks=0");
       DB::table('services')->where('parent_id',$parentId)->whereNotIn('name',$updatedNailsCatNames)->get();

       $updatedNailsCats = DB::table("updated_nails")->get();
       foreach ($updatedNailsCats as $updatedNailsCat) {  
        $serviceName = trim($updatedNailsCat->service);
         $service = DB::table("services")->where('services.name','=',$serviceName)->first(); 
         if($service)
         {
            DB::table("services")->where('services.name','=',$serviceName)->update(['description' => $updatedNailsCat->description, 'tip' => $updatedNailsCat->tips]);
         }
         else
         {
            DB::table("services")->insert(['name' => $serviceName,'description' => $updatedNailsCat->description, 'tip' => $updatedNailsCat->tips,'parent_id' => $parentId]);
         }

       }

    }

    public static function updatesprayTanningSubCat(){
        echo __FUNCTION__ ; echo "\n";
       $parentId = DB::table("services")->where("name","Spray tanning")->first()->id; 


       $updatedsprayTanningCatNames = DB::table("updated_sprayTanning")->pluck('service')->all(); 

       $updatedsprayTanningCatNames = array_map('trim',$updatedsprayTanningCatNames); 
        
       DB::statement("set foreign_key_checks=0");
       DB::table('services')->where('parent_id',$parentId)->whereNotIn('name',$updatedsprayTanningCatNames)->get();

       $updatedsprayTanningCats = DB::table("updated_sprayTanning")->get();
       foreach ($updatedsprayTanningCats as $updatedsprayTanningCat) {  
        $serviceName = trim($updatedsprayTanningCat->service);
         $service = DB::table("services")->where('services.name','=',$serviceName)->first(); 
         if($service)
         {
            DB::table("services")->where('services.name','=',$serviceName)->update(['description' => $updatedsprayTanningCat->description, 'tip' => $updatedsprayTanningCat->tips]);
         }
         else
         {
            DB::table("services")->insert(['name' => $serviceName,'description' => $updatedsprayTanningCat->description, 'tip' => $updatedsprayTanningCat->tips,'parent_id' => $parentId]);
         }

       }

    }

}
