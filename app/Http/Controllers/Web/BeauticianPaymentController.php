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
use App\Http\Requests\Web\BeauticianPaymentDetail;
use App\Providers\PaymentServiceProvider;
use \App\Providers\UserServiceProvider;
use \App\Providers\StripeServiceProvider;

class BeauticianPaymentController extends BaseController {
    
    /**
     * function is used to get beautician already created kit and view to add more kit
     * @return type
     */
    public function getBeauticianPaymentDetail() {
        $userObj = \Auth::user();
        $cardDetail = PaymentServiceProvider::fetchDefaultCardDetail($userObj->stripe_customer_id);
        $bankDetail = PaymentServiceProvider::fetchDefaultBankDetail();
        return view('beautician.payment-details')->with('cardDetail', $cardDetail)->with('bankDetail', $bankDetail);
    }
    
    public function postBeauticianPaymentDetail(BeauticianPaymentDetail $request) {
        $input = $request->all();   
        if(!empty($input['accountNo']) && strlen($input['accountNo']) >= 9) {
            $bankAccountId = PaymentServiceProvider::saveBankDetail($input);
            if(is_array($bankAccountId) && !$bankAccountId['success']) {
                return redirect('beautician/setting/beauticianPaymentDetail')->withErrors($bankAccountId['message']);
            }
            if(!empty($bankAccountId)) {
                $input['bankAccountId'] = $bankAccountId;
                //upload account verification document
                if(is_file($request->file('file'))) {
                    $userId = \Auth::user()->id;
                    $response = UserServiceProvider::uploadFileToTemp($request);
                    $fileName = public_path('temp_images/'.$userId).'/'.$response->getFilename();

                    $response = StripeServiceProvider::attachIdentityVerificationDocument($fileName, $bankAccountId);
                }
            }
        }
        $response = BeauticianServiceProvider::setPaymentDetails($input);
        if($response['success']) {
            return redirect('beautician/setting/beauticianPaymentDetail')->with('status', true)->with('message', $response['message']);
        } else {
            return redirect('beautician/setting/beauticianPaymentDetail')->withErrors($response['message']);
        }
    }
}
