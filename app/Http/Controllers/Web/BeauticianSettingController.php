<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianSettingController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit kumar
 * CreatedOn: date (09/06/2018) 
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Providers\BeauticianServiceProvider;
use App\Http\Requests\Web\BeauticianProfileKitRequest;
use App\Http\Requests\Web\DeleteBeauticianKitRequest;
use App\Http\Requests\Web\BeauticianQualificationNSpecialityRequest;
use App\Http\Requests\Service\SaveExpertiseRequest;

class BeauticianSettingController extends BaseController {


      /**
     * function is used to get beautician already created kit and view to add more kit
     * @return type
     */
    public function getTutorials() {
        return view('beautician.tutorials');
    }
    
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
        return view('beautician.services');
    }
    
}
