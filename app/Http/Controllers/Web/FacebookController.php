<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: FacebookController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit kumar
 * CreatedOn: date (04/08/2018) 
 */

namespace App\Http\Controllers\Web;
require_once base_path('vendor/facebook/graph-sdk/src/Facebook/autoload.php');

use App\Http\Controllers\Controller;
use Facebook\Facebook as Facebook;
use Facebook\Exceptions\FacebookResponseException as FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException as FacebookSDKException;
use Illuminate\Http\Request;

class FacebookController extends Controller {

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
    
    /**
     * function is used to share image on facebook
     * @param Request $request
     * @return type
     */
    public function shareImage(Request $request) {
        
        $appId = env('FB_APP_ID');
        $appSecret = env('FB_APP_SECRET');
        $fb = new Facebook([
                'app_id' => $appId,
                'app_secret' => $appSecret,
                'default_graph_version' => 'v2.2',
                ]);
        $postLoginUrl = env('FB_LOGIN_URL');
    
        $code = (!empty($request->get('code'))?$request->get('code'):'');
        
        if(!empty($request->get('file'))) {
            $shareType = (!empty($request->get('share'))?$request->get('share'):'page');
            $imageFile = $request->get('file');
            $bookingId = $request->get('bookingId');
            \Session::set('share', $shareType);
            \Session::set('file', $request->get('file'));
            \Session::set('bookingId', $bookingId);
        } else {
            $shareType = \Session::get('share');
            $imageFile = \Session::get('file');
            $bookingId = \Session::get('bookingId');
            \Session::forget('share');
            \Session::forget('file');
            \Session::forget('bookingId');
        }
        //Obtain the access_token with publish_stream permission 
        if(empty($code)){ 
            $dialogUrl= "http://www.facebook.com/dialog/oauth?"
             . "client_id=" .  $appId 
             . "&redirect_uri=" . urlencode($postLoginUrl)
             .  "&scope=publish_actions";
            echo "<script>top.location.href='".$dialogUrl. "'</script>";
        } else {
            $tokenUrl ="https://graph.facebook.com/oauth/access_token?"
                            . "client_id=" . $appId 
                            . "&redirect_uri=" . urlencode($postLoginUrl)
                            . "&client_secret=" . $appSecret
                            . "&code=" . $code;
            $response = file_get_contents($tokenUrl);
            $params = null;

            $params = json_decode($response);
            $accessToken = $params->access_token;
            $userId = \Auth::user()->id;
            $resourceDir = public_path('temp_images/'.$userId);
            $resourcePath = $resourceDir.'/'.$imageFile;
            $data = [
                'source' => $fb->fileToUpload($resourcePath),
              ];

            try {
                // Returns a `Facebook\FacebookResponse` object
                if($shareType == 'page') {
                    $beautyjunkiePageId = env('BEAUTYJUNKIE_PAGE_ID');
                } else {
                    $beautyjunkiePageId = 'me';
                }
                $response = $fb->post('/'.$beautyjunkiePageId.'/photos', $data, $accessToken);
                exec('rm -rf '.$resourceDir);
            } catch(FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            $graphNode = $response->getGraphNode();
            return redirect('beautician/booking-details?id='.$bookingId)->with('photoId', $graphNode['id']);
        }
    }

}
