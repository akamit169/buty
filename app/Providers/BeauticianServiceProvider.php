<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: BeauticianServiceProvider.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (14/04/2018) 
 */

namespace App\Providers;

use App\Models\Service;
use App\Models\BeauticianPortfolio;
use App\Models\BeauticianFixhibition;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image as Intervention;
use App\Models\BeauticianKit;
use App\Models\BeauticianQualification;
use App\Models\BeauticianSpeciality;
use App\Models\BeauticianAvailabilitySchedule;
use App\Models\CustomerBooking;
use App\Models\User;
use App\Models\FavouriteBeautician;
use App\Models\BeauticianDetail;
use App\Providers\StripeServiceProvider;
use App\Models\BeauticianService;
use Illuminate\Http\File;

use \App\Utilities\DateTimeUtility;

/**
 * BeauticianServiceProvider class contains methods for user management
 */
class BeauticianServiceProvider extends BaseServiceProvider {

    /**
     * get beautician service list
     *
     * @return type
     */
    public static function getServiceList($beauticianId) {
        try {
            $service = [];
            $service = Service::getServiceList($beauticianId);

            if (count($service) > 0) {
                static::$data['message'] = trans('messages.services.list_fetched_success');
                static::$data['data'] = $service;
                static::$data['success'] = true;
            } else {
                static::$data['success'] = false;
                static::$data['data'] = $service;
                static::$data['message'] = trans('messages.services.list_fetched_failure');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * get sub services list of one or more given parent services
     *
     * @return type
     */
    public static function getSubServices($parentServiceIdArr) {
        try {

            $subservices = Service::whereIn('parent_id', $parentServiceIdArr)->get();

            static::$data['subServices'] = $subservices;
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * get beautician service list
     *
     * @return type
     */
    public static function getTopLevelServices() {
        try {

            $services = Service::whereNull('parent_id')->orderBy('display_order')->get();

            foreach ($services as $service) {
                $service->image = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('SERVICE_IMAGES') . $service->image;
            }

            static::$data['message'] = trans('messages.services.list_fetched_success');
            static::$data['data'] = $services;
            static::$data['success'] = true;
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to save beautician portfolio
     * @param array $requestData
     * @return type
     */
    public static function saveBeauticianPortfolio($requestData) {
    
        try {
            $imageFolder = env('SERVICE_IMAGE_FOLDER');
            $thumbNailFolder = env('SERVICE_THUMBNAIL_FOLDER');


            $imageFileObject = $requestData->file('portfolioPic');
            $input = $requestData->all();
            $imageName = static::uploadFileToS3($imageFolder, $imageFileObject, $thumbNailFolder);

            if ($imageName) {
                $fullImagePath = env('S3_BUCKET_PATH') . env('S3_BUCKET');
                $beauticianModel = new BeauticianPortfolio();
                $beauticianModel->user_id = \Auth::user()->id;
                $beauticianModel->service_id = $input['serviceId'];
                $beauticianModel->image = $imageName;
                $beauticianModel->image_thumbnail = $imageName;
                $status = $beauticianModel->save();
                if ($status) {
                    $beauticianModel->image = $fullImagePath . $imageFolder . $imageName;
                    $beauticianModel->image_thumbnail = $fullImagePath . $thumbNailFolder . $imageName;
                    $beauticianObj = static::getBeauticianPortfolioList(true); //sending true when calling through other function
                    static::$data['message'] = trans('messages.services.image_upload_success');
                    static::$data['data'] = $beauticianObj;
                    static::$data['success'] = true;
                } else {
                    static::$data['message'] = trans('messages.services.image_upload_failure');
                    static::$data['data'] = '';
                    static::$data['success'] = false;
                }
            } else {
                static::$data['message'] = trans('messages.services.image_upload_failure');
                static::$data['data'] = '';
                static::$data['success'] = false;
            }
        } catch (Exception $ex) {
            static::setExceptionError($ex);
        }
        return static::$data;
    }

    /**
     * function is used to upload file to s3 storage
     * @param string $imageFolder
     * @param object $imageFileObject
     * @param string $thumbnailFolder
     * @return If true imagefile name $imageFileName Or boolean $status as false 
     */
    public static function uploadFileToS3($imageFolder, $imageFileObject, $thumbnailFolder = '') {
        
        $status = false;
        try {
            //for large images
            ini_set("memory_limit",-1);
            ini_set('max_execution_time', 600);

            $img = Intervention::make($imageFileObject); 
            $img->orientate();
            $img->save();

            $imageFileName = time() . uniqid() . '.' . $imageFileObject->getClientOriginalExtension();
            $s3 = \Storage::disk('s3');
            $status = $s3->putFileAs(rtrim($imageFolder,"/"),$imageFileObject,$imageFileName, 'public');
            if ($status) {
                if (!empty($thumbnailFolder)) { //if thumbnail folder is passed then upload thumbnail too
                    $imageFileName = static::uploadThumbnailToS3($thumbnailFolder, $imageFileObject, $imageFileName);
                }
                if ($imageFileName) {
                    return $imageFileName;
                }
            }
            return $status;
        } catch (\Exception $e) {
            static::setExceptionError($e);
            return $status;
        }
    }

    /**
     * function is used to upload thumbnail to s3 storage
     * @param string $thumbnailFolder
     * @param object $imageFileObject
     * @return If true imagefile name $imageFileName Or boolean $status as false 
     */
    public static function uploadThumbnailToS3($thumbnailFolder, $imageFileObject, $imageFileName = '') {
        $status = false;


        try {
            $thumbWidthSize = env('THUMBNAIL_WIDTH_SIZE');
            $thumbHeightSize = env('THUMBNAIL_HEIGHT_SIZE');
            $img = Intervention::make($imageFileObject);
            $img->orientate();
            $img->resize($thumbWidthSize, $thumbHeightSize);
            $img->save();
            if (empty($imageFileName)) {
                $imageFileName = time() . '.' . $imageFileObject->getClientOriginalExtension();
            }
            $s3 = \Storage::disk('s3');
            $status = $s3->putFileAs(rtrim($thumbnailFolder,"/"),$imageFileObject,$imageFileName, 'public');
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
     * function is used to delete beautician portfolio image
     * @param int $portfolioId
     * @return type
     */
    public static function deleteBeauticianPortfolio($portfolioId) {
        try {
            $beauticianObj = BeauticianPortfolio::where('id', $portfolioId)->where('user_id',\Auth::user()->id)->first();
            $beauticianImage = env('SERVICE_IMAGE_FOLDER') . $beauticianObj->image;
            $beauticianThumbnail = env('SERVICE_THUMBNAIL_FOLDER') . $beauticianObj->image_thumbnail;
            $status = false;


            if (\Storage::disk('s3')->delete([$beauticianImage, $beauticianThumbnail])) {
                $status = BeauticianPortfolio::where('id', $portfolioId)->delete();
            }
           


            if ($status) {
                $userId = \Auth::user()->id;
                $arrLatestPortfolio = static::fetchLatestBeauticianPortfolio($userId);
                static::$data['message'] = trans('messages.beautician.portfolio_delete_success');
                static::$data['data'] = $arrLatestPortfolio;
                static::$data['success'] = true;
            } else {
                static::$data['message'] = trans('messages.beautician.portfolio_delete_failure');
                static::$data['data'] = [];
                static::$data['success'] = false;
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to get beautician portfolio list
     */
    public static function getBeauticianPortfolioList($otherFunctionCall = false) {
        try {
            $userId = \Auth::user()->id;
            $imagePath = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('SERVICE_IMAGE_FOLDER');
            $thumbnailPath = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('SERVICE_THUMBNAIL_FOLDER');
            $beauticianPortfolioObj = BeauticianPortfolio::where('user_id', $userId)
                            ->select('beautician_portfolios.id as portfolio_id', 'beautician_portfolios.user_id', 'beautician_portfolios.service_id', DB::raw('IF(beautician_portfolios.image="", "", CONCAT("' . $imagePath . '",beautician_portfolios.image)) as image'), DB::raw('IF(beautician_portfolios.image_thumbnail="", "", CONCAT("' . $thumbnailPath . '",beautician_portfolios.image_thumbnail)) as image_thumbnail'))
                            ->orderBy('beautician_portfolios.id', 'desc')->get()->toArray();
            if (!empty($beauticianPortfolioObj)) {
                $arrNewBeauticianPortfolio = [];
                $arrNewBeauticianPortfolio['arrPortfolioList'] = static::arrangeServiceBasedBeauticianPortfolio($beauticianPortfolioObj);
                $arrNewBeauticianPortfolio['arrLatestPortfolioList'] = array_slice($beauticianPortfolioObj, 0, 16);
                if ($otherFunctionCall) {
                    return $arrNewBeauticianPortfolio;
                }
                static::$data['message'] = trans('messages.beautician.portfolio_list_success');
                static::$data['data'] = $arrNewBeauticianPortfolio;
                static::$data['success'] = true;
            } else {
                static::$data['message'] = trans('messages.beautician.portfolio_list_failure');
                static::$data['success'] = false;
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to get beautician portfolio of a given service
     */
    public static function getBeauticianPortfolioByService($serviceId) {
        try {
            $userId = \Auth::user()->id;
            $imagePath = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('SERVICE_IMAGE_FOLDER');
            $thumbnailPath = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('SERVICE_THUMBNAIL_FOLDER');
            $beauticianPortfolio = BeauticianPortfolio::where('user_id', $userId)
                            ->where('beautician_portfolios.service_id', $serviceId)
                            ->select('beautician_portfolios.id as portfolio_id', 'beautician_portfolios.user_id', 'beautician_portfolios.service_id', DB::raw('IF(beautician_portfolios.image="", "", CONCAT("' . $imagePath . '",beautician_portfolios.image)) as image'), DB::raw('IF(beautician_portfolios.image_thumbnail="", "", CONCAT("' . $thumbnailPath . '",beautician_portfolios.image_thumbnail)) as image_thumbnail'))
                            ->orderBy('beautician_portfolios.id', 'desc')->get();

            static::$data['message'] = trans('messages.beautician.portfolio_list_success');
            static::$data['data'] = $beauticianPortfolio;
            static::$data['success'] = true;
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to save or delete beautician kit
     * @param array $input
     * @return type
     */
    public static function saveDeleteBeauticianKit($input) {
        try {
            $arrKitName = (isset($input['kitName']) ? $input['kitName'] : array());
            $arrDeletedKitId = (isset($input['deletedKitId']) ? $input['deletedKitId'] : array());
            $userObj = \Auth::user();
            $status = false;
            $deletedStatus = false;
            if (count($arrDeletedKitId) > 0) {
                //delete the selected kit of the beautician
                $deletedStatus = BeauticianKit::whereIn('id', $arrDeletedKitId)->delete();
            }
            if (count($arrKitName) > 0) {
                //save the new kit name
                $arrKitDetail = [];
                foreach ($arrKitName as $value) {
                    array_push($arrKitDetail, ['user_id' => $userObj->id, 'kit_name' => $value]);
                }
                $status = BeauticianKit::insert($arrKitDetail);
            }
            if ($status) {
                $arrBeauticianKit = BeauticianKit::where('user_id', $userObj->id)->orderBy('id', 'desc')->get();
                static::$data['message'] = trans('messages.beautician.kit_save_success');
                static::$data['data'] = $arrBeauticianKit;
                static::$data['success'] = true;
            } else if ($deletedStatus) {
                $arrBeauticianKit = BeauticianKit::where('user_id', $userObj->id)->orderBy('id', 'desc')->get();
                static::$data['message'] = trans('messages.beautician.kit_deleted_success');
                static::$data['data'] = $arrBeauticianKit;
                static::$data['success'] = true;
            } else {
                static::$data['message'] = trans('messages.beautician.kit_save_failure');
                static::$data['data'] = '';
                static::$data['success'] = false;
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to get beautician kit list
     * @return type
     */
    public static function getBeauticianKitList($beauticianId) {
        try {
            $beauticianKitObj = BeauticianKit::where('user_id', $beauticianId)->orderBy('id', 'desc')->get();
            if (!empty($beauticianKitObj)) {
                static::$data['message'] = trans('messages.beautician.kit_list_success');
                static::$data['data'] = $beauticianKitObj;
                static::$data['success'] = true;
            } else {
                static::$data['message'] = trans('messages.beautician.kit_list_failure');
                static::$data['success'] = false;
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to get beautician expertise
     * @return type
     */
    public static function getExpertise($beauticianId) {
        try {


            static::$data['qualifications'] = BeauticianQualification::where('user_id', '=', $beauticianId)->get();
            static::$data['specialities'] = BeauticianSpeciality::where('user_id', '=', $beauticianId)->get();

            static::$data['message'] = trans('messages.record_listed');
            static::$data['success'] = true;
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to save beautician expertise
     * @return type
     */
    public static function saveExpertise($input) {
        try {

            $userObj = \Auth::user();

            static::$data['qualifications'] = static::saveQualifications($userObj, $input);
            static::$data['specialities'] = static::saveSpecialities($userObj, $input);
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    public static function saveQualifications($userObj, $input) {
        $arrQualification = (isset($input['qualification']) ? $input['qualification'] : array());
        $arrDeletedQualificationId = (isset($input['deletedQualificationId']) ? $input['deletedQualificationId'] : array());

        $status = false;
        $deletedStatus = false;
        if (count($arrDeletedQualificationId) > 0) {
            //delete the selected qualification of the beautician
            $deletedStatus = BeauticianQualification::whereIn('id', $arrDeletedQualificationId)
                            ->where('user_id',$userObj->id)->delete();
        }
        if (count($arrQualification) > 0) {
            //save the new qualification name
            $arrQualificationDetail = [];
            foreach ($arrQualification as $value) {
                array_push($arrQualificationDetail, ['user_id' => $userObj->id, 'qualification' => $value]);
            }
            $status = BeauticianQualification::insert($arrQualificationDetail);
        }


        $qualifications = BeauticianQualification::where('user_id', $userObj->id)->get();

        return $qualifications;
    }

    public static function saveSpecialities($userObj, $input) {
        $arrSpeciality = (isset($input['speciality']) ? $input['speciality'] : array());
        $arrDeletedSpecialityId = (isset($input['deletedSpecialityId']) ? $input['deletedSpecialityId'] : array());

        $status = false;
        $deletedStatus = false;
        if (count($arrDeletedSpecialityId) > 0) {
            //delete the selected speciality of the beautician
            $deletedStatus = BeauticianSpeciality::whereIn('id', $arrDeletedSpecialityId)
                                                    ->where('user_id',$userObj->id)->delete();
        }
        if (count($arrSpeciality) > 0) {
            //save the new speciality name
            $arrSpecialityDetail = [];
            foreach ($arrSpeciality as $value) {
                array_push($arrSpecialityDetail, ['user_id' => $userObj->id, 'speciality' => $value]);
            }
            $status = BeauticianSpeciality::insert($arrSpecialityDetail);
        }


        $speciality = BeauticianSpeciality::where('user_id', $userObj->id)->get();

        return $speciality;
    }

    /**
     * function is used to array of beautician portfolio based on service
     * @param type $arrBeauticianList
     * @return type
     */
    public static function arrangeServiceBasedBeauticianPortfolio($arrBeauticianList) {

        $arrNewBeauticianList = array();
        foreach ($arrBeauticianList as $value) {
            $arrValue = ['portfolio_id' => $value['portfolio_id'], 'service_id' => $value['service_id'],
                'image' => $value['image'], 'image_thumbnail' => $value['image_thumbnail']];

            if (array_key_exists($value['service_id'], $arrNewBeauticianList)) {
                array_push($arrNewBeauticianList[$value['service_id']]['arrImage'], $arrValue);
            } else {
                $arrNewValue = ['service_id' => $value['service_id']];
                $arrNewValue['arrImage'] = [$arrValue];
                $arrNewBeauticianList[$value['service_id']] = $arrNewValue;
            }
        }
        $arrNewBeauticianList = array_values($arrNewBeauticianList);
        return $arrNewBeauticianList;
    }

   /**
     * function is used to fetch latest beautician uploaded portfolio
     * @return type
     */
    public static function fetchLatestBeauticianPortfolio($userId) {
       
        $imagePath = env('S3_BUCKET_PATH').env('S3_BUCKET').env('SERVICE_IMAGE_FOLDER');
        $thumbnailPath = env('S3_BUCKET_PATH').env('S3_BUCKET').env('SERVICE_THUMBNAIL_FOLDER');
            
        $arrLatestPortfolio = BeauticianPortfolio::join('services','services.id','=','beautician_portfolios.service_id')
                                                ->where('user_id', $userId)
                                                ->select('beautician_portfolios.id as portfolioId', 'beautician_portfolios.user_id', 
                                                         'beautician_portfolios.service_id', 'services.name as serviceName',
                                                            DB::raw('IF(beautician_portfolios.image="", "", CONCAT("'.$imagePath.'",beautician_portfolios.image)) as image'), 
                                                            DB::raw('IF(beautician_portfolios.image_thumbnail="", "", CONCAT("'.$thumbnailPath.'",beautician_portfolios.image_thumbnail)) as image_thumbnail')
                                                        )
                                                ->orderBy('beautician_portfolios.id', 'desc')
                                                ->get()->toArray();
        return $arrLatestPortfolio;
    }

    /**
     * set up business profile
     * @return type
     */
    public static function setupBusinessProfile($data) {
        try {

            DB::beginTransaction();

            $user = \Auth::user();

            if(!isset($data['profilePic']) && $user->profile_pic == "")
            {
                static::$data['message'] = 'Profile Pic is required';
                static::$data['success'] = false;
                return static::$data;
            }


            $user->address = isset($data['address']) ? $data['address'] : '';
            $user->suburb = $data['suburb'];
            $user->state = $data['state'];
            $user->country = $data['country'];
            $user->lat = $data['lat'];
            $user->lng = $data['lng'];
            if (isset($data['postalCode'])) {
                $user->zipcode = $data['postalCode'];
            }
            if (isset($data['phone'])) {
                $user->phone_number = $data['phone'];
            }
            $user->save();

            $beauticianDetails = BeauticianDetail::where('user_id', '=', $user->id)->first();

            if (!empty($data['businessDescription'])) {
                $beauticianDetails->business_description = $data['businessDescription'];
            }
            $beauticianDetails->work_radius = isset($data['workRadius']) ? $data['workRadius'] : 0;
            $beauticianDetails->mobile_services = (isset($data['mobileServices']) && !empty($data['mobileServices'])) ? 1 : 0;

            if (isset($data['instaId'])) {
                $beauticianDetails->instagram_link = $data['instaId'];
            }
            if (isset($data['crueltyFreeMakeup'])) {
                $beauticianDetails->cruelty_free_makeup = $data['crueltyFreeMakeup'];
            }
            $beauticianDetails->save();

            if($user->profile_pic)
            {
                $user->profile_pic = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('USER_PROFILE_PIC_S3') . $user->profile_pic;

            }
        
            static::$data['message'] = trans('messages.update_successful');
            static::$data['user'] = $user;
            static::$data['user']['beautician_details'] = $beauticianDetails;


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * update business description
     * @return type
     */
    public static function updateBusinessDescription($data) {
        try {

            $user = \Auth::user();

            BeauticianDetail::where('user_id', '=', $user->id)
                    ->update(['business_description' => $data['businessDescription']]);

            static::$data['message'] = trans('messages.update_successful');
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to save service for beautician
     * @param array $data
     * @return array
     */
    public static function saveBeauticianService($data) {
        try {
            $user = \Auth::user();
            static::$data['success'] = BeauticianService::saveBeauticianService($data, $user->id);
            static::$data['service'] = static::getBeauticianServicesUtil($user->id, $data['serviceId']);
            if (static::$data['success']) {
                static::$data['message'] = trans('messages.beautician.service_created_success');
            } else {
                static::$data['message'] = trans('messages.beautician.service_created_failure');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to delete service created by beautician
     * @param array $data
     * @return array
     */
    public static function deleteBeauticianService($data) {
        try {
            $user = \Auth::user();
            static::$data['success'] = BeauticianService::where('id', $data['beauticianServiceId'])
                            ->where('beautician_id', $user->id)->delete();
            if (static::$data['success']) {
                static::$data['message'] = trans('messages.beautician.service_deleted_success');
            } else {
                static::$data['message'] = trans('messages.beautician.service_deleted_failure');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to get all the services created by beautician
     */
    public static function getBeauticianService($beauticianId) {
        try {

            $arrBeauticianService = static::getBeauticianServicesUtil($beauticianId);
            if ($arrBeauticianService) {
                static::$data['data'] = $arrBeauticianService;
                static::$data['success'] = true;
                static::$data['message'] = trans('messages.beautician.service_fetched_success');
            } else {
                static::$data['data'] = [];
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.beautician.service_fetched_failure');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    public static function getBeauticianServicesUtil($beauticianId, $serviceId = false) {
        $query = BeauticianService::join('services as ps', 'ps.id', '=', 'beautician_services.parent_service_id')
                ->join('services as cs', 'cs.id', '=', 'beautician_services.service_id')
                ->where('beautician_services.beautician_id', $beauticianId)
                ->select('beautician_services.*', 'ps.name as parent_service_name', 'cs.name as service_name');
        if ($serviceId) {
            $query->where('beautician_services.service_id', $serviceId);
        }

        $arrBeauticianService = $query->get()->toArray();


        if (count($arrBeauticianService) > 0) {
            //fetch all the parent category
            $arrBeauticianService = static::buildServiceTree($arrBeauticianService);
        }


        if ($serviceId && count($arrBeauticianService) == 1) {
            $arrBeauticianService = $arrBeauticianService[0]['children'][0];
        }

        return $arrBeauticianService;
    }

    /**
     * function is used to merge all child service into one parent service
     * @param array $arrBeauticianService
     */
    public static function buildServiceTree($arrBeauticianService) {

        $newArrBeauticianService = [];
        $currentDt = strtotime(date('Y-m-d H:i:s'));
        foreach ($arrBeauticianService as $value) {
            $value['discounted_price'] = 0;
            //check if discount is available on current date or not
            if ($currentDt >= strtotime($value['discount_startdate']) && $currentDt <= strtotime($value['discount_enddate'])) {
                $value['is_discount_available'] = 1;
                $value['discounted_price'] = $value['cost'] * $value['discount'] / 100;
                $value['discounted_price'] = $value['cost'] - $value['discounted_price'];
            } else {
                $value['is_discount_available'] = 0;
            }
            if (array_key_exists($value['parent_service_id'], $newArrBeauticianService)) {
                $newArrBeauticianService[$value['parent_service_id']]['childCount'] += 1;
                array_push($newArrBeauticianService[$value['parent_service_id']]['children'], $value);
            } else {
                $newArrBeauticianService[$value['parent_service_id']]['name'] = $value['parent_service_name'];
                $newArrBeauticianService[$value['parent_service_id']]['childCount'] = 1;
                $newArrBeauticianService[$value['parent_service_id']]['children'][] = $value;
            }
        }
        $newArrBeauticianService = array_values($newArrBeauticianService);
        return $newArrBeauticianService;
    }

    /**
     * function is used to update service for beautician
     * @param array $data
     * @return array
     */
    public static function updateBeauticianService($data) {
        try {
            static::$data['success'] = BeauticianService::updateBeauticianService($data);
            if (static::$data['success']) {
                static::$data['message'] = trans('messages.beautician.service_updated_success');
            } else {
                static::$data['message'] = trans('messages.beautician.service_updated_failure');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to save beautician pro availability
     * @param type $data
     * @return type
     */
    public static function setAvailability($data) {
        try {
            $user = \Auth::user();
            static::$data['success'] = BeauticianAvailabilitySchedule::saveBeauticianAvailability($data, $user->id);
            if (static::$data['success']) {
                static::$data['message'] = trans('messages.beautician.availability_scheduled_success');
            } else {
                static::$data['message'] = trans('messages.beautician.availability_scheduled_failure');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to update service tips and description for beautician
     * @param array $data
     * @return array
     */
    public static function updateServiceDescriptionTips($data) {
        try {
            $user = \Auth::user();

            BeauticianService::where('beautician_id', '=', $user->id)
                    ->where('service_id', '=', $data['serviceId'])
                    ->update(['description' => $data['description'], 'tip' => $data['tip']]);

            if (static::$data['success']) {
                static::$data['message'] = trans('messages.beautician.service_updated_success');
            } else {
                static::$data['message'] = trans('messages.beautician.service_updated_failure');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to get beautician about us data and portfolio info
     * @param array $data
     * @return array
     */
    public static function getBeauticianDetails($beauticianId, $customerId = false, $portfolio = true) {
        try {
            $profilePicBasePath = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('USER_PROFILE_PIC_S3');
            $query = User::join('beautician_details', function($join) use($beauticianId) {
                        $join->on('users.id', '=', 'beautician_details.user_id')
                        ->where('users.id', $beauticianId);
                    })
                    ->leftjoin('beautician_services', function($join) use($beauticianId) {
                $join->on('beautician_services.beautician_id', '=', 'users.id')
                ->where('users.id', $beauticianId)
                ->whereNull('beautician_services.deleted_at');
            });

            $query->select(DB::raw("IF(users.profile_pic IS NULL || users.profile_pic='', '', CONCAT('" . $profilePicBasePath . "',users.profile_pic)) as profile_pic"), 'users.first_name', 'users.last_name', 'users.address', 'users.suburb','users.state', 'users.country', 'users.zipcode', 'users.rating', 'users.review_count', 'work_radius','mobile_services','abn', 'business_name', 'business_description', 'users.phone_number', 'beautician_details.instagram_link','beautician_details.mobile_services', 'beautician_details.cruelty_free_makeup', DB::raw('COUNT(*) as serviceCount'), DB::raw('MIN(beautician_services.cost) as minCharge'), DB::raw('MAX(beautician_services.cost) as maxCharge'))->groupBy('users.id');

            if ($customerId) {
                $customerCoordinates = User::where('id', $customerId)->select('lat', 'lng')->first();

                $distance = "6371 * acos( cos( radians('$customerCoordinates->lat') ) * 
                      cos( radians( users.lat ) ) * 
                      cos( radians( users.lng ) - 
                      radians('$customerCoordinates->lng') ) + 
                      sin( radians('$customerCoordinates->lat') ) * 
                      sin( radians( users.lat ) ) )";

                $query->addSelect(DB::raw("ROUND($distance) as distance"));

                $user = $query->first();
                $favourite = FavouriteBeautician::where('customer_id', '=', $customerId)->where('beautician_id', '=', $beauticianId)->count();

                $user->is_favourite = $favourite ? 1 : 0;
            } else {
                $user = $query->first();
                $user->is_favourite = 0;
            }

            $user->travelCost = (float) DB::table('admin_settings')
                            ->where('config_key', '=', 'travel_cost')->pluck('config_value')->first();

            if ($portfolio) {
                $user->portfolioPhotos = static::fetchLatestBeauticianPortfolio($beauticianId);
            }

            static::$data['beauticianDetails'] = $user;
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to save beautician fixhibition
     * @param array $requestData
     * @return type
     */
    public static function saveBeauticianFixhibition($requestData) {
        try {
            $imageFolder = env('FIXHIBITION_IMAGE_FOLDER');
            $thumbNailFolder = env('FIXHIBITION_THUMBNAIL_FOLDER');
            $imageFileObject = $requestData->file('fixhibitionImage');
            $imageName = static::uploadFileToS3($imageFolder, $imageFileObject, $thumbNailFolder);
            if ($imageName) {
                $fullImagePath = env('S3_BUCKET_PATH') . env('S3_BUCKET');
                $beauticianFixhibitionModel = new BeauticianFixhibition();
                $beauticianFixhibitionModel->user_id = \Auth::user()->id;
                $beauticianFixhibitionModel->image = $imageName;
                $beauticianFixhibitionModel->image_thumbnail = $imageName;
                $status = $beauticianFixhibitionModel->save();
                if ($status) {
                    $beauticianFixhibitionModel->fixhibition_id = $beauticianFixhibitionModel->id;
                    $beauticianFixhibitionModel->image = $fullImagePath . $imageFolder . $imageName;
                    $beauticianFixhibitionModel->image_thumbnail = $fullImagePath . $thumbNailFolder . $imageName;
                    static::$data['message'] = trans('messages.beautician.image_upload_success');
                    static::$data['data'] = $beauticianFixhibitionModel;
                    static::$data['success'] = true;
                } else {
                    static::$data['message'] = trans('messages.beautician.image_upload_failure');
                    static::$data['data'] = '';
                    static::$data['success'] = false;
                }
            } else {
                static::$data['message'] = trans('messages.beautician.image_upload_failure');
                static::$data['data'] = '';
                static::$data['success'] = false;
            }
        } catch (Exception $ex) {
            static::setExceptionError($ex);
        }
        return static::$data;
    }

    /**
     * function is used to get beautician fixhibition list
     */
    public static function getBeauticianFixhibitionList($my = null) {
        try {

            $userObj = \Auth::user();
            $userId = $userObj->id;
            $perPage = config('constants.PER_PAGE');
            $imagePath = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('FIXHIBITION_IMAGE_FOLDER');
            $profilePicBasePath = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('USER_PROFILE_PIC_S3');
            $defaultImage = url('assets/beautician/images/profile_default.jpg');
            $thumbnailPath = env('S3_BUCKET_PATH') . env('S3_BUCKET') . env('FIXHIBITION_THUMBNAIL_FOLDER');
            $beauticianFixhibitionObj = BeauticianFixhibition::join('users', function($join) {
                        $join->on('users.id', '=', 'beautician_fixhibition.user_id')
                        ->where('users.status', User::IS_ACTIVE)
                        ->whereNull('users.deleted_at');
                    })
                    ->join('beautician_details', 'beautician_details.user_id', '=', 'users.id')
                    ->select('users.first_name', 'users.last_name', 'beautician_details.business_name', 'beautician_fixhibition.created_at as createdAt', 'beautician_fixhibition.id as fixhibition_id', 'beautician_fixhibition.user_id', DB::raw('IF(users.profile_pic="", "' . $defaultImage . '", CONCAT("' . $profilePicBasePath . '",users.profile_pic)) as profile_pic'), DB::raw('IF(beautician_fixhibition.image="", "", CONCAT("' . $imagePath . '",beautician_fixhibition.image)) as image'), DB::raw('IF(beautician_fixhibition.image_thumbnail="", "", CONCAT("' . $thumbnailPath . '",beautician_fixhibition.image_thumbnail)) as image_thumbnail'));
            if ($my) {
                $beauticianFixhibitionObj->where('beautician_fixhibition.user_id', '=', $userId);
                $data = $beauticianFixhibitionObj->orderBy('beautician_fixhibition.id', 'desc')->paginate(BeauticianFixhibition::FIXHIBITION_UPLOAD_LIMIT)->toArray();
            } else {
                $beauticianFixhibitionObj->where('beautician_fixhibition.user_id', '!=', $userId);
                $data = $beauticianFixhibitionObj->orderBy('beautician_fixhibition.id', 'desc')->paginate($perPage)->toArray();
            }


            if (!empty($data)) {
                static::$data['message'] = trans('messages.beautician.fixhibition_fetched_success');
                static::$data['data'] = $data;
                static::$data['success'] = true;
            } else {
                static::$data['data'] = [];
                static::$data['message'] = trans('messages.beautician.fixhibition_fetched_failure');
                static::$data['success'] = false;
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to delete fixhibition created by beautician
     * @param array $data
     * @return array
     */
    public static function deleteBeauticianFixhibition($data) {
        try {
            $user = \Auth::user();
            static::$data['success'] = false;
            $objFixihibitionImage = BeauticianFixhibition::where('id', $data['beauticianFixhibitionId'])
                            ->where('user_id', $user->id)->first();

            if (!empty($objFixihibitionImage)) {
                $fixihibitionImagePath = env('FIXHIBITION_IMAGE_FOLDER');
                $fixihibitionThumbPath = env('FIXHIBITION_THUMBNAIL_FOLDER');
                $arrImage = [$fixihibitionImagePath . $objFixihibitionImage->image, $fixihibitionThumbPath . $objFixihibitionImage->image_thumbnail];
                $status = static::deleteS3Images($arrImage);

                if ($status) {
                    static::$data['success'] = BeauticianFixhibition::where('id', $data['beauticianFixhibitionId'])
                                    ->where('user_id', $user->id)->delete();
                }
            }

            if (static::$data['success']) {
                static::$data['message'] = trans('messages.beautician.fixhibition_deleted_success');
            } else {
                static::$data['message'] = trans('messages.beautician.fixhibition_deleted_failure');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to get beautician available Data
     * @param array $data
     * @return array
     */
    public static function getAvailability($date) {
        try {
            $user = \Auth::user();
            $userId = $user->id;
            $data = BeauticianAvailabilitySchedule::where('start_datetime', '>=', $date)->where('beautician_id', '=', $userId)->orderBy(DB::RAW("date(start_datetime)"), 'ASC')->orderBy('slot', 'ASC')->get()->toArray();
            $bookingData = CustomerBooking::where('start_datetime', '>=', $date)->where('beautician_id', '=', $userId)->get()->toArray();
            foreach ($data as $key => $availabilityArr) {
                $isBooked = 0;
                foreach ($bookingData as $custBooking) {
                    $availabilityStartTime = strtotime($availabilityArr['start_datetime']);
                    $availabilityEndTime = strtotime($availabilityArr['end_datetime']);
                    $custBookingTime = strtotime($custBooking['start_datetime']);
                    if ($custBookingTime > $availabilityStartTime && $custBookingTime < $availabilityEndTime) {
                        $isBooked = 1;
                        break;
                    }
                }
                $data[$key]['is_booked'] = $isBooked;
            }
            if (!empty($data)) {
                static::$data['message'] = trans('messages.beautician.availabilty_fetched_success');
                static::$data['data'] = $data;
                static::$data['success'] = true;
            } else {
                static::$data['data'] = [];
                static::$data['message'] = trans('messages.beautician.availabilty_fetched_failure');
                static::$data['success'] = false;
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to save account id and customer id
     * @param  $data
     * @return type
     */
    public static function setPaymentDetails($data) {
        try {
            $user = \Auth::user();
            static::$data['success'] = false;
            if (isset($data['cardToken'])) {
                $cardToken = $data['cardToken'];

                static::$data = StripeServiceProvider::registerUserOnStripe($user->email, $cardToken);
            }
            if (static::$data['success'] == true) {
                $user->stripe_customer_id = static::$data['customerId'];
            }
            if (isset($data['bankAccountId'])) {
                static::$data['success'] = true;
                
                if(isset($data['stripeVerificationDoc']))
                {
                     StripeServiceProvider::attachIdentityVerificationDocument($data['stripeVerificationDoc']->getPathName(),$data['bankAccountId']);
                }
               
                $user->stripe_bank_account_id = $data['bankAccountId'];
            }
            $user->save();
            static::$data['message'] = trans('messages.beautician.payment_detail_updated_success');
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

  /**
     * function used to get beautician availability along with booked slots
     * @param  $data
     * @return type
     */
    public static function getBeauticianBookingAvailability($data)
    {
         try {
           $startDateTime = $data['startDateTime'];
           $endDateTime = $data['endDateTime'];
           $beauticianId = $data['beauticianId'];

          $utcOffset = DateTimeUtility::getStandardOffsetUTC($data['timezone']);

          $travelTimePerKm = DB::table('admin_settings')
                            ->where('config_key','travel_time_km')->pluck('config_value')->first();

          $bookingAvailability = BeauticianAvailabilitySchedule::leftJoin('customer_bookings',function($join) use ($beauticianId,$utcOffset){
                $join->on('customer_bookings.beautician_id','=','beautician_availability_schedule.beautician_id')
                     ->on(DB::raw('date(convert_tz(customer_bookings.start_datetime,"+00:00","'.$utcOffset.'"))'),'=',DB::raw('date(convert_tz(beautician_availability_schedule.start_datetime,"+00:00","'.$utcOffset.'"))'))
                     ->on(DB::raw('date(convert_tz(customer_bookings.end_datetime,"+00:00","'.$utcOffset.'"))'),'=',DB::raw('date(convert_tz(beautician_availability_schedule.start_datetime,"+00:00","'.$utcOffset.'"))'))
                     ->where('customer_bookings.beautician_id',$beauticianId)
                     ->whereNotIn('customer_bookings.status',[CustomerBooking::IS_CANCELLED,CustomerBooking::PAYMENT_FAILED]);
           })
           ->where('beautician_availability_schedule.start_datetime','>=',$startDateTime)
           ->where('beautician_availability_schedule.end_datetime','<=',$endDateTime)
           ->where('beautician_availability_schedule.beautician_id','=',$beauticianId)
           ->where('beautician_availability_schedule.is_available',BeauticianAvailabilitySchedule::IS_AVAILABLE)
           ->orderBy(DB::RAW("beautician_availability_schedule.start_datetime"),'ASC')
           ->orderBy('slot','ASC')
           ->select('beautician_availability_schedule.start_datetime','beautician_availability_schedule.end_datetime','customer_bookings.start_datetime as bookingStartDateTime','customer_bookings.end_datetime as bookingEndDateTime','beautician_availability_schedule.id','customer_bookings.id as customerBookingId',DB::raw('customer_bookings.distance * '.$travelTimePerKm.' as distance'))
           ->get();


           $bookingAvailabilityArr = [];

            if(count($bookingAvailability) > 0)
            {
                $bookingAvailabilityArr[] = ['availability' => [], 'booking' => [] ];

                $availabilityComparisionId = 0;
                $bookingComparisionIdArr = [];
                $comparisionDate = "";
                $i=-1;


                foreach ($bookingAvailability as $value) {

                    //change date to the given timezone in order to compare the exact dates
                     $localDateTime = DateTimeUtility::convertDateTimeToTimezone($value->start_datetime, $data['timezone']);

                     $localDateTimeString = date('Ymd',strtotime($localDateTime));

                     //increment array index in case the start dates are different
                     if($localDateTimeString != $comparisionDate)
                     {
                        $i++; 
                        $bookingAvailabilityArr[$i]['booking'] = [];  
                        $bookingComparisionIdArr = [];  

                     }

                     //populate the availability array
                     if($value->id != $availabilityComparisionId)
                        {
                            $bookingAvailabilityArr[$i]['availability'][] = 
                             [
                                'startDateTime' => $value->start_datetime,
                                'endDateTime' => $value->end_datetime
                             ];

                             $availabilityComparisionId = $value->id;
                        }

                        //populate the booking array
                        if(!in_array($value->customerBookingId,$bookingComparisionIdArr) &&  $value->bookingStartDateTime!= null)
                        {    
                             $bookingAvailabilityArr[$i]['booking'][] = 
                             [
                                'startDateTime' => $value->bookingStartDateTime,
                                'endDateTime' => $value->bookingEndDateTime,
                                'travelTime' => $value->distance
                             ];

                             array_push($bookingComparisionIdArr, $value->customerBookingId);

                        }

                        //update the comparision date
                        $comparisionDate = $localDateTimeString;

                     
                     
                  }
                }  

            static::$data['bookingAvailability'] =  $bookingAvailabilityArr;       

        } catch (\Exception $e) { 
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to get beautician add qualification
     * @param int $beauticianId | Optional
     * @return type
     */
    public static function getBeauticianQualification($beauticianId = '') {
        static::$data['data'] = [];
        static::$data['success'] = false;
        try {
            if (empty($beauticianId)) {
                $beauticianId = \Auth::user()->id;
            }
            static::$data['data'] = BeauticianQualification::where('user_id', $beauticianId)->get()->toArray();
            if (count(static::$data['data']) > 0) {
                static::$data['success'] = true;
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to get beautician added specialities
     * @param int $beauticianId | Optional
     * @return type
     */
    public static function getBeauticianSpecialities($beauticianId = '') {
        static::$data['data'] = [];
        static::$data['success'] = false;
        try {
            if (empty($beauticianId)) {
                $beauticianId = \Auth::user()->id;
            }
            static::$data['data'] = BeauticianSpeciality::where('user_id', $beauticianId)->get()->toArray();
            if (count(static::$data['data']) > 0) {
                static::$data['success'] = true;
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to get lat and long by address
     * @param array $data
     * @return type
     */
    public static function getLatLongByAddress($data) {
        static::$data['data'] = [];
        static::$data['success'] = false;
        try {
            $data['address'] = str_replace(' ', '+', trim($data['address']));
            $data['suburb'] = str_replace(' ', '+', trim($data['suburb']));
            $data['country'] = str_replace(' ', '+', trim($data['country']));
            $address = $data['address'] . ',' . $data['suburb'] . ',' . $data['country'];
            $address = trim($address, ',');
            static::$data['data'] = parent::getLatLongByAddress($address);
            if (count(static::$data['data']) > 0) {
                static::$data['success'] = true;
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to get beautician's current booking of current date
     * @param array $input
     * @return type
     */
    public static function getBeauticianCurrentBooking($input) {
        try {
            $userObj = \Auth::user();
            $startDateTime = $input['startDateTime'];
            $showPastBookings = $input['showPastBookings'];

            static::$data['bookingDetails'] = [];
            static::$data['bookingDetails'] = CustomerBooking::getBeauticianBookingDetails($startDateTime, $showPastBookings, $userObj->id);



            $query = BeauticianAvailabilitySchedule::where('beautician_id', '=', $userObj->id);

            if ($showPastBookings == 0) {
                $query->where('start_datetime', '>=', $startDateTime);
            } else {
                $query->where('start_datetime', '<=', $startDateTime);
            }

            static::$data['availabilityDetails'] = $query->orderBy(DB::RAW("date(start_datetime)"), 'ASC')
                            ->orderBy('slot', 'ASC')->get()->toArray();

            if (count(static::$data['bookingDetails']) > 0) {
                static::$data['message'] = trans('messages.customer.booking_available');
            } else {
                static::$data['success'] = false;
                static::$data['message'] = trans('messages.customer.no_booking_available');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }

        return static::$data;
    }

    /**
     * function is used to get beautician price range
     * @param int $beauticianId
     * @return array
     */
    public static function getBeauticianPriceRange($beauticianId) {
        static::$data['data'] = [];
        static::$data['success'] = false;
        static::$data['message'] = trans('messages.beautician.price_range_failure');
        try {
            static::$data['data'] = BeauticianService::getBeauticianPriceRange($beauticianId);
            if (count(static::$data['data']) > 0) {
                static::$data['success'] = true;
                static::$data['message'] = trans('messages.beautician.price_range_success');
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * get beautician availability data by dates
     * @param type $date
     * @return type
     */
    public static function getBeauticianAvailabilityData($date, $timezone) {
        try {
            $userId = \Auth::user()->id;
            $utcOffset = DateTimeUtility::getStandardOffsetUTC($timezone);
            if (!is_array($date)) {
                $date = [$date];
            }
            $arrData = BeauticianAvailabilitySchedule::where('beautician_id', $userId)
                            ->whereIn(DB::raw('date(convert_tz(start_datetime, "+00:00", "' . $utcOffset . '"))'), $date)
                            ->orderBy(DB::raw('date(convert_tz(start_datetime, "+00:00", "' . $utcOffset . '"))'), 'asc')
                            ->orderBy('slot', 'asc')
                            ->get()->toArray();
            return $arrData;
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * validate if beautician has been booked for given date
     * @param string $date
     */
    public static function validateBooking($date, $timezone) {
        static::$data['data'] = [];
        static::$data['success'] = static::$data['bookedFlag'] = 0;
        static::$data['message'] = trans('messages.beautician.unable_to_edit_availability');
        try {
            $userId = \Auth::user()->id;
            $utcOffset = DateTimeUtility::getStandardOffsetUTC($timezone);
            $arrObj = CustomerBooking::where('beautician_id', $userId)->where(DB::raw('date(convert_tz(start_datetime, "+00:00", "' . $utcOffset . '"))'), $date)->first();
            static::$data['success'] = true;
            if (!empty($arrObj)) {
                static::$data['bookedFlag'] = 1;
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

    /**
     * function is used to fetch beautician available dates
     */
    public static function getBeauticianAvailabilityDates($timezone) {
        static::$data['data'] = [];
        static::$data['success'] = false;
        static::$data['message'] = trans('messages.beautician.no_availability_found');
        try {
            $userId = \Auth::user()->id;
            $date = date('Y-m-d');
            $utcOffset = DateTimeUtility::getStandardOffsetUTC($timezone);
            $arrData = BeauticianAvailabilitySchedule::where('beautician_id', $userId)->where(DB::raw('date(convert_tz(start_datetime, "+00:00", "' . $utcOffset . '"))'), '>=', $date)
                            ->select(DB::raw('start_datetime'))->get()->toArray();
            if (count($arrData) > 0) {
                static::$data['success'] = true;
                foreach ($arrData as $key => $value) {
                    $arrData[$key] = DateTimeUtility::convertDateTimeToTimezone($value['start_datetime'], $timezone);
                    $arrData[$key] = date('Y-m-d', strtotime($arrData[$key]));
                }
                static::$data['data'] = $arrData;
            }
        } catch (\Exception $e) {
            static::setExceptionError($e);
        }
        return static::$data;
    }

}
