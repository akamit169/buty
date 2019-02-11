<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianProfileController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit kumar
 * CreatedOn: date (06/06/2018) 
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Providers\BeauticianServiceProvider;
use App\Http\Requests\Web\BeauticianProfileKitRequest;
use App\Http\Requests\Web\DeleteBeauticianKitRequest;
use App\Http\Requests\Web\BeauticianQualificationNSpecialityRequest;
use App\Http\Requests\Service\SaveExpertiseRequest;
use App\Http\Requests\Web\SetupBusinessProfileRequest;
use App\Http\Requests\Web\GetAddressLatLong;
use App\Providers\UserServiceProvider;
use App\Http\Requests\Web\AvailabilityViewRequest;
use App\Http\Requests\Service\BeauticianSetAvailabilityRequest;
use Illuminate\Http\Request;

class BeauticianProfileController extends BaseController {
    
    /**
     * function is used to get beautician already created kit and view to add more kit
     * @return type
     */
    public function getBeauticianProfileKit() {
        $userObj = \Auth::user();
        $beauticianKit = BeauticianServiceProvider::getBeauticianKitList($userObj->id);
        return view('beautician.profile-kit')->with('beauticianKit', $beauticianKit['data']->toArray());
    }
    
    /**
     * function is used to save beautician kit
     */
    public function saveBeauticianProfileKit(BeauticianProfileKitRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::saveDeleteBeauticianKit($input);
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to delete beautician kit
     */
    public function deleteBeauticianKit(DeleteBeauticianKitRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::saveDeleteBeauticianKit($input);
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to get beautician expertise and to save new ones
     * @return type
     */
    public function getBeauticianExpertise() {
        $userObj = \Auth::user();
        $arrBeauticianSpecialities = BeauticianServiceProvider::getBeauticianSpecialities($userObj->id);
        $arrBeauticianQualification = BeauticianServiceProvider::getBeauticianQualification($userObj->id);
        
        return view('beautician.profile-expertise')->with('arrBeauticianSpecialities', $arrBeauticianSpecialities)
                ->with('arrBeauticianQualification', $arrBeauticianQualification);
    }
    
    /**
     * function is used to save beautician qualification and speciality
     * @return type
     */
    public function saveBeauticianQualificationNSpeciality(BeauticianQualificationNSpecialityRequest $request) {
        $input = $request->all(); $qualificationResponse = $specialityResponse = $response = []; 
        $response['success'] = false; $response['data'] = [];
        $response['message'] = trans('messages.beautician.save_qualification_speciality_failure');
        $response['status_code'] = \Symfony\Component\HttpFoundation\Response::HTTP_OK;
        $userObj = \Auth::user();
        if(!empty($input['qualification'])) {
            $qualificationResponse = BeauticianServiceProvider::saveQualifications($userObj, $input);
        }
        if(!empty($input['speciality'])) {
            $specialityResponse = BeauticianServiceProvider::saveSpecialities($userObj, $input);
        }
        if(count($qualificationResponse) > 0 || count($specialityResponse) > 0) {
            $response['message'] = trans('messages.beautician.save_qualification_speciality_success');
            $response['data']['arrQualification'] = $qualificationResponse;
            $response['data']['arrSpeciality'] = $specialityResponse;
            $response['success'] = true;
        }
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to delete beautician expertise i.e. qualification and speciality
     * @param SaveExpertiseRequest $request
     * @return type
     */
    public function deleteBeauticianExpertise(SaveExpertiseRequest $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::saveExpertise($input);
        return $this->sendJsonResponse($response);
    }


    /**
     * function is used to get the services offered by the beautician
     * @return type
     */
    public function getServices() {
        $user = \Auth::user();
        $response = BeauticianServiceProvider::getTopLevelServices();
        $serviceResponse = BeauticianServiceProvider::getBeauticianService($user->id);
        return view('beautician.services')->with('services',$response['data'])
                                          ->with('beauticianServices',$serviceResponse['data']);
    }
    
    /**
     * function is used to get Beautician Profile Detail
     * @return type
     */
    public function getBeauticianProfile() {
        $userObj = \Auth::user();
        $beauticianDetail = BeauticianServiceProvider::getBeauticianDetails($userObj->id);
        $statesResponse = UserServiceProvider::getStates();
        return view('beautician.edit-profile')->with('beauticianDetail', $beauticianDetail)
                                              ->with('states',$statesResponse['states']);
    }
    
    /**
     * function is used to update beautician profile
     * @param SetupBusinessProfileRequest $request
     * @return type
     */
    public function postBeauticanProfile(SetupBusinessProfileRequest $request) {

        $input = $request->all();
        if(!empty($request->file('profilePic'))) {
            $response = UserServiceProvider::postUserProfilePic($request->file('profilePic'));
        }
        $response = BeauticianServiceProvider::setupBusinessProfile($input);
        if($response['success']) {
            return redirect('beautician/setting/editProfile')->with('status', true)->with('message', $response['message']);
        } else {
            return redirect('beautician/setting/editProfile')->withErrors($response['message']);
        }
    }
    
    /**
     * function is used to get lat and long by address
     * @param GetAddressLatLong $request
     * @return type
     */
    public function getAddressLatLong(GetAddressLatLong $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::getLatLongByAddress($input);
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to render beautician availability view
     * @return type
     */
    public function getBeauticianAvailability() {
        $user = \Auth::user();
        $arePaymentDetailsSet = !empty($user->stripe_bank_account_id)?1:0;
        return view('beautician.availability')->with('arePaymentDetailsSet',$arePaymentDetailsSet);
    }
    
    public function getAvailabilityView(AvailabilityViewRequest $request) {
        $input = $request->all();
        $arrSelectedDate = [$input['selectedDate']];
        $response = BeauticianServiceProvider::getBeauticianAvailabilityData($arrSelectedDate);
        return view('beautician.availability-view')->with('data', $response)->with('selectedDate', $input['selectedDate']);
    }
    
    /**
     * function is used to register beautician and on successful register, login user
     * @param BeauticianRegistrationRequest $request
     * @return type
     */
    public function postSetAvailability(BeauticianSetAvailabilityRequest $request) {
        $input = $request->all(); 
        $response = BeauticianServiceProvider::setAvailability($input['arrAvailabilityDetails']);
        return $this->sendJsonResponse($response);
    }
    
    public function getAvailabilityData(Request $request) {
        $input = $request->all();
        $arrSelectedDate = $input['selectedDate'];
        $timezone = $input['timezone'];
        $returnFromSave = (isset($input['returnFromSave'])?$input['returnFromSave']:0);
        foreach($arrSelectedDate as $key=>$value) {
            $arrSelectedDate[$key] = date('Y-m-d', strtotime($value));
        }
        $response = BeauticianServiceProvider::getBeauticianAvailabilityData($arrSelectedDate, $timezone);
        return view('beautician.availability-view')->with('data', $response)->with('selectedDate', $input['selectedDate'])
                ->with('timezone', $timezone)->with('returnFromSave', $returnFromSave);
    }
    
    /**
     * function is used to check if beautician selected date is booked by customer or not
     * @param Request $request
     * @return type
     */
    public function checkBooking(Request $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::validateBooking($input['date'], $input['timezone']);
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to get beautician availability dates
     * @return type
     */
    public function getBeauticianAvailableDates(Request $request) {
        $input = $request->all();
        $response = BeauticianServiceProvider::getBeauticianAvailabilityDates($input['timezone']);
        return $this->sendJsonResponse($response);
    }
    
}
