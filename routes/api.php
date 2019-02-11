<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['middleware'=>'XSS'], function () {

     Route::group(['prefix' => 'customer'], function () {

        Route::post('userRegistration', 'Service\CustomerController@postUserRegistration');
        Route::post('userLogin', 'Service\CustomerController@postUserLogin');

        Route::get('privacy-policy', 'Service\CustomerController@getPrivacyPolicy');
        Route::get('terms-and-conditions', 'Service\CustomerController@getTermsAndConditions');

        Route::group(['middleware'=>'App\Http\Middleware\ApiAuth'],function(){
            Route::post('signupReferral', 'Service\CustomerController@signupReferral');
            Route::post('userLogout', 'Service\CustomerController@postUserLogout');
            Route::post('setupProfile', 'Service\CustomerController@setupProfile');
            Route::get('appearanceData', 'Service\CustomerController@getAppearanceData');
            Route::post('registerUserOnStripe', 'Service\CustomerController@registerUserOnStripe');
            Route::get('searchBeautician', 'Service\CustomerController@searchBeautician');
            Route::get('getBeauticianExpertise', 'Service\CustomerController@getBeauticianExpertise');
            Route::get('getBeauticianKit', 'Service\CustomerController@getBeauticianKit');
            Route::get('getBeauticianServices', 'Service\CustomerController@getBeauticianServices');
            Route::get('getBeauticianDetails', 'Service\CustomerController@getBeauticianDetails');
            Route::get('getBeauticianFixhibition', 'Service\CustomerController@getBeauticianFixhibition');
            Route::get('getBeauticianBookingAvailability', 'Service\CustomerController@getBeauticianBookingAvailability');
            Route::post('bookService', 'Service\ServiceBookingController@bookService');
            Route::post('markBeauticianFavourite', 'Service\CustomerController@markBeauticianFavourite');
            Route::get('getFavouriteBeauticians', 'Service\CustomerController@getFavouriteBeauticians');
            Route::get('getCustomerCurrentBooking', 'Service\CustomerController@getCustomerCurrentBooking');
            Route::delete('customerCurrentBooking', 'Service\CustomerController@deleteCustomerCurrentBooking');
        });

     });


     Route::get('testUpdateSubcats', 'Service\UserController@testUpdateSubcats');

     Route::group(['prefix' => 'user'], function () {

         Route::post('testStripeCharge', 'Service\UserController@testCharge');
         Route::post('forgotPassword', 'Service\UserController@postForgotPassword');


         Route::group(['middleware'=>'App\Http\Middleware\ApiAuth'],function(){
                Route::post('changePassword', 'Service\UserController@postChangePassword');
                Route::post('profilePic', 'Service\UserController@postUserProfilePic');
                Route::post('rateReviewUser', 'Service\ServiceBookingController@postRateReviewUser');
                Route::get('ratingReason', 'Service\UserController@getRatingReason');
                Route::get('userPreviousRating', 'Service\UserController@getUserPreviousRating');
                Route::get('bookings', 'Service\ServiceBookingController@getUserBookings');
                Route::get('notifications', 'Service\NotificationController@getNotificationList');
                Route::post('raiseDispute', 'Service\UserController@raiseDispute');
                Route::post('cancelBooking', 'Service\ServiceBookingController@cancelBooking');
                Route::get('getUserPendingFeedback', 'Service\ServiceBookingController@getUserPendingFeedback');
                Route::get('getBookingSummary', 'Service\ServiceBookingController@getBookingSummary');
                Route::get('details', 'Service\UserController@getUserDetails');
         });

     });


     Route::group(['prefix' => 'beautician'], function () {


        Route::get('privacy-policy', 'Service\BeauticianController@getPrivacyPolicy');
        Route::get('terms-and-conditions', 'Service\BeauticianController@getTermsAndConditions');

     	Route::group(['middleware'=>'App\Http\Middleware\ApiAuth'],function(){

             Route::post('setupBusinessProfile', 'Service\BeauticianController@setupBusinessProfile');
             Route::post('updateBusinessDescription', 'Service\BeauticianController@updateBusinessDescription');
             Route::post('saveBeauticianPortfolio', 'Service\BeauticianController@postBeauticianPortfolio');
             Route::delete('beauticianPortfolio', 'Service\BeauticianController@deleteBeauticianPortfolio');
             Route::get('getBeauticianPortfolioList', 'Service\BeauticianController@getBeauticianPortfolioList');
             Route::post('saveBeauticianKit', 'Service\BeauticianController@postSaveBeauticianKit');
             Route::get('getBeauticianKit', 'Service\BeauticianController@getBeauticianKit');
             Route::get('getExpertise', 'Service\BeauticianController@getExpertise');
             Route::post('saveExpertise', 'Service\BeauticianController@saveExpertise');
             Route::post('createService', 'Service\BeauticianController@postCreateService');
             Route::delete('deleteService', 'Service\BeauticianController@deleteService');
             Route::get('getService', 'Service\BeauticianController@getService');
             Route::post('updateService', 'Service\BeauticianController@postUpdateService');
             Route::post('setAvailability', 'Service\BeauticianAvailabilityController@postSetAvailability');
             Route::post('updateAvailability', 'Service\BeauticianAvailabilityController@postUpdateAvailability');
             Route::get('getAvailability', 'Service\BeauticianAvailabilityController@getAvailability');
             Route::post('updateServiceDescriptionTips', 'Service\BeauticianController@updateServiceDescriptionTips');
             Route::get('getAllFixhibition', 'Service\BeauticianController@getAllFixhibition');
             Route::get('getMyFixhibition', 'Service\BeauticianController@getMyFixhibition');
             Route::post('saveBeauticianFixhibition', 'Service\BeauticianController@postBeauticianFixhibition');
             Route::delete('deleteFixhibition', 'Service\BeauticianController@deleteFixhibition');

             Route::post('setPaymentDetails', 'Service\BeauticianController@setPaymentDetails');
             Route::get('getCustomerDetails', 'Service\BeauticianController@getCustomerDetails');
             Route::get('getPriceRange', 'Service\BeauticianController@getPriceRange');

             Route::get('getBeauticianCurrentBooking', 'Service\BeauticianController@getBeauticianCurrentBooking');

             Route::post('setBeauticianTimeliness', 'Service\NotificationController@setBeauticianTimeliness');



     	});

     	Route::post('userRegistration', 'Service\BeauticianController@postUserRegistration');
    	Route::post('sendRegisteredEmail', 'Service\BeauticianController@postSendRegisteredEmail');
     });
     
     Route::group(['prefix' => 'service'], function () {
        
        Route::group(['middleware'=>'App\Http\Middleware\ApiAuth'],function(){
            Route::get('getServiceList', 'Service\ServiceController@getServicesList');
            Route::post('markServiceComplete', 'Service\ServiceBookingController@postMarkServiceComplete');
        });
        
        Route::get('list', 'Service\ServiceController@getTopLevelServices');
     	Route::get('subServices', 'Service\ServiceController@getSubServices');
     });

     Route::group(['prefix' => 'notification'], function () {
        Route::group(['middleware'=>'App\Http\Middleware\ApiAuth'],function(){
            Route::post('markRead', 'Service\NotificationController@markRead');
            Route::delete('delete', 'Service\NotificationController@deleteNotification');
            Route::get('count', 'Service\NotificationController@getCount');
        }); 
     });

    Route::group(['middleware'=>'App\Http\Middleware\ApiAuth'],function(){
        Route::post('flag/user', 'Service\UserController@postFlagUser');
        Route::get('flag/reasons', 'Service\UserController@getFlagReasons');
    });


     Route::get('states', 'Service\UserController@getStates');
    


});

