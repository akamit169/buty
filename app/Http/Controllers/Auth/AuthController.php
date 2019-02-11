<?php
/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: AuthController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (30/05/2018) 
 */

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\LoginUserRequest;
use Auth;

class AuthController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Registration & Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles the registration of new users, as well as the
      | authentication of existing users. By default, this controller uses
      | a simple trait to add these behaviors. Why don't you explore it?
      |
     */

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
                    'name' => 'required|max:255',
                    'email' => 'required|email|max:255|unique:users',
                    'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data) {
        return User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Show the login view.

     */
    public function getLogin() {
        return view('admin.login');
    }

    /**
     * Authenticate user after that login.
     *
     * @param  array  $request
     * @return User
     */
    public function postLogin(LoginUserRequest $request) {


        $response = \UserService::loginUser($request->all());

        if ($response['success'] == true) {
            $userType = Auth::user()->user_type;
            $resetPassword = Auth::user()->reset_password;
            $redirectObj = new \StdClass();
            if ((isset($userType) && $userType == config('constants.USER_TYPE.admin')) && (isset($resetPassword) && $resetPassword == config('constants.RESET_PASSWORD.active'))) {

                $redirectObj = redirect('admin/user/change-password');
            } elseif ((isset($userType) && $userType == config('constants.USER_TYPE.admin'))) {
                $redirectObj = redirect('admin/dashboard');
            } else {
                $errorMsg = trans('messages.invalid_login_credentials');
                $redirectObj = redirect('auth/login')->with('error_msg', $errorMsg);
            }

            return $redirectObj;
        } else {
            return redirect()->back()->with('error_msg', $response['message']);
        }
    }

}
