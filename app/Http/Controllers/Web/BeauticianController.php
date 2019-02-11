<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit Yadav
 * CreatedOn: date (04/04/2018) 
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Web\ResetPasswordRequest;
use App\Http\Requests\Web\SignUpRequest;
use App\Providers\UserServiceProvider;
use App\Providers\BookingServiceProvider;
use App\Http\Requests\Web\ForgotPasswordRequest;
use App\Http\Requests\Web\LoginRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\ChangePasswordRequest;
use App\Providers\BeauticianServiceProvider;
use App\Http\Requests\Service\BeauticianFixhibitionRequest;
use App\Http\Requests\Service\DeleteBeauticianFixhibition;
use App\Http\Requests\Web\BeauticianPortfolioRequest;
use App\Http\Requests\Web\CreateBeauticianServiceRequest;
use \Validator;
use App\Http\Requests\Web\CustomerRatingRequest;
use App\Http\Requests\Web\RateReviewUserRequest;
use \App\Models\Service;

class BeauticianController extends BaseController {

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct() {
        //Log out Back
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
    }

    /*
     * get beautician home page
     */

    public function getHome() {
        $services = UserServiceProvider::getBeauticianServiceImages(Service::HAIR_ID);
        $topLevelServices = BeauticianServiceProvider::getTopLevelServices();
        return view('beautician.home')->with('services', $services)->with('topLevelServices',$topLevelServices['data']);
    }

    /**
     * get signup view
     * @return type
     */
    public function getSignup() {
        return view('beautician.signup');
    }

    /**
     * get view on signup success
     * @return type
     */
    public function getSignupSuccess() {
        return view('beautician.signup-success');
    }

    /*
     * get settings view
     */

    public function fixhibitions() {
        return view('beautician.fixhibition');
    }


    public function getProfile() {
        $beauticianId = \Auth::user()->id;
        $response = BeauticianServiceProvider::getBeauticianDetails($beauticianId);
        $services = BeauticianServiceProvider::getTopLevelServices()['data'];
        return view('beautician.profile')->with('response',$response)->with('services',$services);
    }

    /*
     * get welcome screen view 
     */

    public function getWelcomeScreen() {
        return view('beautician.welcome-screen');
    }


      /*
     * get welcome screen view 
     */

    public function getPortfolioUpload() {
        $response = BeauticianServiceProvider::getTopLevelServices();
        return view('beautician.upload-work')->with('services',$response['data']);
    }

    


    /**
     * get beautician details request
     * @return type
     */
    public function getBeauticianDetailsAjax() {
        $beauticianId = \Auth::user()->id;
        $response = BeauticianServiceProvider::getBeauticianDetails($beauticianId);
        return $this->sendJsonResponse($response);
    }

    /**
     * sign up beautician
     * @param SignUpRequest $request
     * @return type
     */
    public function postSignup(SignUpRequest $request) {

        $input = $request->all();
        $certFileName = UserServiceProvider::uploadFileToS3($request->file('certificate'));
        $input['certificateFileName'] = $certFileName;
        $response = UserServiceProvider::registerBeautician($input);
        return $response;
    }

    /**
     * get Login view
     */
    public function getLogin() {
        return view('beautician.login');
    }

    /**
     * login beautician
     * @param LoginRequest $request
     * @return type
     */
    public function postLogin(LoginRequest $request) {
        $input = $request->all();
        $input['user_type'] = User::IS_BEAUTICIAN;
        $response = UserServiceProvider::loginUser($input);
        $redirectUrlObject = new \StdClass();
        if ($response['success'] == true) {
            $adminApprovalStatus = \Auth::user()->admin_approval_status;
            if ($adminApprovalStatus == USER::IS_APPROVED) {
                return redirect('beautician/setting/editProfile');
            } else {
                switch ($adminApprovalStatus) {
                    case USER::IS_APPROVAL_PENDING:
                        $redirectUrlObject = redirect('beautician/waiting-approval');
                        break;
                    case USER::IS_DISAPPROVED:
                        $redirectUrlObject = redirect('beautician/approval-rejected');
                        break;
                    default:
                       $redirectUrlObject = redirect()->back()->with('error_msg', $response['message'])->withInput();

                }

                return $redirectUrlObject;
            }
        } else {
            return redirect()->back()->with('error_msg', $response['message'])->withInput();
        }
    }

    /*
     * redirect to login view
     */

    public function getLogout() {
        \Auth::logout();
        return redirect('beautician');
    }

    /*
     * get privacy policy view
     */

    public function getPrivacyPolicy() {
        return view('beautician.privacy-policy');
    }

    /*
     * get terms and conditions view
     */

    public function getTermsAndConditions() {
        return view('beautician.terms-and-conditions');
    }

    /*
     * get forgot password view
     */

    public function getForgotPassword() {
        return view('beautician.forgot-password');
    }

    /**
     * forgot password handling
     * @param ForgotPasswordRequest $request
     * @return type
     */
    public function postForgotPassword(ForgotPasswordRequest $request) {
        $input = $request->all();
        $response = UserServiceProvider::forgotPassword($input);
        if ($response['success'] == true) {
            return redirect('beautician/login')->with('success_msg', $response['message']);
        } else {
            return redirect()->back()->with('error_msg', $response['message'])->withInput();
        }
    }

    /**
     * get view for resetting password
     * @param Request $request
     * @return type
     */
    public function getResetPassword(Request $request) {
        $input = $request->all();
        $response = UserServiceProvider::getUserByResetPasswordToken($input);
        return view('beautician.reset-password', $response);
    }

    /**
     * reset password post request
     * @param ResetPasswordRequest $request
     * @return type
     */
    public function postResetPassword(Request $request) {
        $input = $request->all();
        $rules = array(
            'password' => 'required|max:12|string|regex:/(?=.*[a-zA-Z0-9])(?=.*[!$#%@])/|same:confirmPassword|min:4',
            'confirmPassword' => 'required|max:12',
        );
        $messages = [
            'password.min' => 'Password should be minimum 4 characters and maximum 12 characters containing at least one special character (!, $, #,%,@).',
            'password.max' => 'Password should be minimum 4 characters and maximum 12 characters containing at least one special character (!, $, #,%,@).',
            'password.string' => 'Password should be minimum 4 characters and maximum 12 characters containing at least one special character (!, $, #,%,@).',
            'password.regex' => 'Password should be minimum 4 characters and maximum 12 characters containing at least one special character (!, $, #,%,@).',
        ];
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->with('error_msg', $validator->errors()->first());
        } else {
            $response = UserServiceProvider::resetPassword($input);
            if ($response['success'] == true) {
                return redirect('beautician/login')->with('success_msg', $response['message']);
            } else {
                return redirect()->back()->with('error_msg', $response['message']);
            }
        }
    }

    /**
     * change password
     * @param ChangePasswordRequest $request
     * @return type
     */
    public function postChangePassword(ChangePasswordRequest $request) {
        $input = $request->all();
        $response = UserServiceProvider::changePassword($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to get the list of Fixhibitions created by beautician
     * @return type
     */
    public function getAllFixhibition() {

        $response = BeauticianServiceProvider::getBeauticianFixhibitionList();
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to get the list of My Fixhibition created by beautician
     * @return type
     */
    public function getMyFixhibition() {

        $myFixhibition = true;
        $response = BeauticianServiceProvider::getBeauticianFixhibitionList($myFixhibition);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to save beautician Fixhibition
     * @param BeauticianPortfolioRequest $request
     * @return type
     */
    public function postBeauticianFixhibition(BeauticianFixhibitionRequest $request) {
        $response = BeauticianServiceProvider::saveBeauticianFixhibition($request);
        return $this->sendJsonResponse($response);
    }

    /**
     * function is used to delete Fixhibition for beautician
     * @param DeleteBeauticianServiceRequest $request
     * @return type
     */
    public function deleteFixhibition(DeleteBeauticianFixhibition $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::deleteBeauticianFixhibition($input);
        return $this->sendJsonResponse($response);
    }

    /**
     * get view on approval awaited by admin
     * @return type
     */
    public function getWaitingScreen() {
        return view('beautician.waiting-message');
    }

    /**
     * get view on approval rejected by admin
     * @return type
     */
    public function getRejectedScreen() {
        return view('beautician.rejected-message');
    }

    /*
     * get beautician home page
     */

    public function getServiceImages(Request $request) {
        $id = $request->input('service_id');
        $services = UserServiceProvider::getBeauticianServiceImages($id);
        return view('beautician.beautician-services-partial')->with('services', $services);
    }

    /**
     * function is used to get list of beautician portfolio list 
     * @return type
     */
    public function getBeauticianPortfolioList() {
        $response = BeauticianServiceProvider::getBeauticianPortfolioList();
        return $this->sendJsonResponse($response);
    }


     /**
     * function is used to get list of beautician portfolio of a given service 
     * @return type
     */
    public function getBeauticianPortfolioByService(Request $request) {
        $serviceId = $request->input('serviceId');
        $response = BeauticianServiceProvider::getBeauticianPortfolioByService($serviceId);
        return $this->sendJsonResponse($response);
    }


     /**
     * function is used to save beautician portfolio
     * @param BeauticianPortfolioRequest $request
     * @return type
     */
    public function postBeauticianPortfolio(BeauticianPortfolioRequest $request) {
        $response = BeauticianServiceProvider::saveBeauticianPortfolio($request);
        if($response['success'] == true)
        {
          return redirect('beautician/profile');
        }
        else
        {
             return redirect()->back()->with('error_msg', $response['message']);
        }
    }





    /**
     * function is used to create service for beautician
     * @param CreateBeauticianServiceRequest $request
     * @return type
     */
    public function postCreateService(CreateBeauticianServiceRequest $request) {
        $input = $request->all(); 
        $response = BeauticianServiceProvider::saveBeauticianService($input);
        if($response['success'] == true)
        {
            return redirect("beautician/profile/services");
        }
        else
        {
            return redirect()->back()->with('error_msg',$response['message']);
        }
    }

    /**
     * function is used to get rating of an user
     * @param CustomerRatingRequest $request
     */
    public function getCustomerRating(CustomerRatingRequest $request) {
        $input = $request->all();
        $response = UserServiceProvider::fetchUserPreviousRating($input, true);
        if($response['success'] == true)
        {
            return view("beautician.rate_review_list")->with('rating', $response['data'])->with('bookingId', $input['bookingId'])->with('userId', $input['userId']);
        } else {
            //fetch booking detail
            $bookingDetail = BookingServiceProvider::getBookingDetailsById(array($input['bookingId']));
            if(count($bookingDetail)>0) {
                $bookingDetail = array_values($bookingDetail);
                return view("beautician.rate_review_list")->with('rating', $bookingDetail[0])->with('bookingId', $input['bookingId'])->with('userId', $input['userId']);
            }
            return redirect()->back()->with('error_msg',$response['message']);
        }
    }
    
    /**
     * function is used to render the view to rate and review an user
     * @param CustomerRatingRequest $request
     * @return type
     */
    public function getRateReviewUser(CustomerRatingRequest $request) {
        $input = $request->all();
        //check if rating is already done or not
        $ratingObj = UserServiceProvider::validateRating($input['bookingId']);
        if(!empty($ratingObj)) {
            return redirect('beautician/getCustomerRating?bookingId='.$input['bookingId'].'&userId='.$input['userId']);
        }
        $reasonMaster = UserServiceProvider::fetchRatingReason();
        $bookingDetail = BookingServiceProvider::getBookingDetailsById(array($input['bookingId'])); 
        if(count($bookingDetail)>0) {
            $bookingDetail = $bookingDetail[$input['userId']][0];
        }
        
        return view('beautician.rate_view_user')->with('bookingId', $input['bookingId'])->with('userId', $input['userId'])->with('reasonMaster', $reasonMaster['reason'])
                ->with('bookingDetail', $bookingDetail);
    }
    
    /**
     * function is used to save rate and review of an user
     * @param RateReviewUserRequest $request
     * @return type
     */
    public function postRateReviewUser(RateReviewUserRequest $request) {
        $input = $request->all();
        $response = BookingServiceProvider::rateReviewUser($input);
        if($response['success']) {
            return redirect("beautician/getCustomerRating?bookingId=".$input['bookingId'].'&userId='.$input['userId'])->with('success_msg', $response['message']);
        } else {
            return redirect()->back()->with('error_msg',$response['message']);
        }
    }
}
