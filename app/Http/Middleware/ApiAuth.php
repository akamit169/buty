<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: ApiAuth.php
 * CodeLibrary/Project: NA/BeautyJunkie
 * Author:Abhijeet
 * CreatedOn: date (dd/mm/yyyy) 
 */

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\BaseController;
use App\Models\UserDevice;

class ApiAuth {

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request            
     * @param \Closure $next            
     * @return mixed
     */
    public function handle($request, Closure $next) {

        $user = UserDevice::where('user_devices.access_token', '=', $request->header('accessToken'))
                ->where('user_devices.device_type', '=', $request->header('deviceType'))
                ->first();

        if (!$user) {
            $response['message'] = trans('messages.unauthorised');
            $response['success'] = false;
            $response['status_code'] = \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED;
            $baseController = new BaseController();
            return $baseController->sendJsonResponse($response);
        }

        \Auth::loginUsingId($user->user_id);
        return $next($request);
    }

}
