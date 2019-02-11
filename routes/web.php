  <?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */


Route::group(['middleware' => 'App\Http\Middleware\Clickjacking'], function() {
    
    Route::get('/test', function () {
        return view('email.user.forgot_password2');
    });

    Route::get('/', function () {
        return redirect('auth/login');
    });

    
     Route::group(['prefix' => 'customer'], function () {
       Route::get('resetPassword', 'Web\CustomerController@getResetPassword');
       Route::post('reset-password', 'Web\CustomerController@postResetPassword');

       Route::get('privacy-policy', 'Web\CustomerController@getPrivacyPolicy');
       Route::get('terms-and-conditions', 'Web\CustomerController@getTermsAndConditions');

     });

  

    
    Route::post('auth/login', 'Auth\AuthController@postLogin')->middleware('guest');
    Route::get('auth/login', 'Auth\AuthController@getLogin')->middleware('guest');
    Route::get('monthly-report', 'Web\CronController@sendMonthlyReportToBeautician');
    Route::get('generate-rating-graph', 'Web\CronController@generateBeauticianRatingGraph');
    Route::get('generate-complete-service-graph', 'Web\CronController@generateBeauticianCompletedServiceGraph');
    Route::get('testPushNotification', 'Service\UserController@testPushNotification')->middleware('guest');
    Route::get('share-image', 'Web\InstagramController@shareImage');
    Route::group(['prefix' => 'admin'], function () {
        Route::post('user/email', 'Admin\AdminController@postTemporaryPassword');
        Route::get('user/forget-password', 'Admin\AdminController@getTemporaryPassword');
        
        Route::group(['middleware' => ['auth']], function () {
            Route::get('user/change-password', 'Admin\AdminController@getChangePassword');
            Route::post('user/change-password', 'Admin\AdminController@postChangePassword');
            
            Route::get('user/change-password-by-dasboard', 'Admin\AdminController@getChangePasswordByDasboard');
            Route::post('user/change-password-by-dasboard', 'Admin\AdminController@postChangePasswordByDasboard');
            
            Route::group(['prefix' => 'user'], function () {
                Route::get('export-user', 'Admin\UserController@exportUser');
                Route::get('export-beautician/{userStatus}', 'Admin\UserController@exportBeautician');
                Route::get('get-suspended-user-list', 'Admin\UserController@getSuspendedUserList');
                Route::get('suspended-user-list-ajax', 'Admin\UserController@getSuspendedUserListAjax');
                Route::get('suspend-unsuspend-user/{userId}', 'Admin\UserController@getSuspendUnsuspendUser');
                Route::get('get-flagged-user-list', 'Admin\UserController@getFlaggedUserList');
                Route::get('flagged-user-list-ajax', 'Admin\UserController@getFlaggedUserListAjax');
                Route::get('user-reported-by-list/{id}', 'Admin\UserController@getUserReportedByList');
                Route::get('unflag/{id}', 'Admin\UserController@getUnflag');
                Route::get('customers-revenue', 'Admin\UserController@getCustomersRevenue');
                Route::get('customers-revenue-list-ajax', 'Admin\UserController@getCustomersRevenueListAjax');
                Route::get('beautician-revenue', 'Admin\UserController@getBeauticianRevenue');
                Route::get('beautician-revenue-list-ajax', 'Admin\UserController@getBeauticianRevenueListAjax');
            });
            Route::get('referred-user-list', 'Admin\UserController@getReferredUserList');
            Route::get('referred-user-list-ajax', 'Admin\UserController@getReferredUserListAjax');
            Route::get('logout', 'Admin\AdminController@getlogout');
            Route::get('dashboard', 'Admin\AdminController@getDashboard');
            Route::resource('user', 'Admin\UserController');
            Route::get('beautician/get-beautician-list', 'Admin\BeauticianController@getBeauticianList');
            Route::get('beautician/beautician-list-ajax', 'Admin\BeauticianController@getBeauticianListAjax');
            Route::get('beautician/view-beautician/{id}', 'Admin\BeauticianController@getViewBeautician');
            Route::get('beautician/approve-beautician/{id}', 'Admin\BeauticianController@approveBeautician');
            Route::get('beautician/reject-beautician/{id}', 'Admin\BeauticianController@rejectBeautician');
            Route::get('beautician/approved-beautician-list', 'Admin\BeauticianController@getApprovedBeauticianList');
            Route::get('beautician/approved-beautician-list-ajax', 'Admin\BeauticianController@getApprovedBeauticianListAjax');
            Route::get('beautician/rejected-beautician-list', 'Admin\BeauticianController@getRejectedBeauticianList');
            Route::get('beautician/rejected-beautician-list-ajax', 'Admin\BeauticianController@getRejectedBeauticianListAjax');

             Route::post('beautician/resolve-dispute', 'Admin\ServiceBookingController@resolveDispute');
             Route::post('beautician/reject-dispute', 'Admin\ServiceBookingController@rejectDispute');
             
             Route::get('app-settings', 'Admin\SettingsController@getAdminSettings');

             Route::get('commission-settings', 'Admin\SettingsController@getCommissionSettings');
             Route::get('get-beautypro-global-percent', 'Admin\SettingsController@getBeautyProGlobalPercent');
             Route::get('get-states', 'Admin\SettingsController@getStates');
             Route::get('get-premium-beautypros', 'Admin\SettingsController@getPremiumBeautyPros');
             Route::post('save-state-commissions', 'Admin\SettingsController@saveStateCommissions');
             Route::get('get-service-commissions', 'Admin\SettingsController@getServiceCommissions');
             Route::post('save-service-commissions', 'Admin\SettingsController@saveServiceCommissions');
             Route::post('save-premium-service-commissions', 'Admin\SettingsController@savePremiumServiceCommissions');
             Route::post('remove-premium-commissions', 'Admin\SettingsController@removePremiumCommissions');
             Route::post('set-beautypro-global-commissions', 'Admin\SettingsController@setBeautyProGlobalPercent');
             Route::post('set-beautypro-premium-global-commissions', 'Admin\SettingsController@setBeautyProPremiumGlobalPercent');
             Route::get('get-beautypro-listing', 'Admin\SettingsController@getBeautyProListing');
             Route::get('get-beautician-service-commissions', 'Admin\SettingsController@getBeauticianServiceCommissions');

             Route::post('modify-setting', 'Admin\SettingsController@modifyAdminSetting');
             Route::get('app-settings-list-ajax', 'Admin\SettingsController@getAdminSettingsListAjax');
            
            Route::group(['prefix' => 'customer'], function () {
                Route::get('get-customer-list', 'Admin\CustomerController@getCustomerList');
                Route::get('customer-list-ajax', 'Admin\CustomerController@getCustomerListAjax');
                Route::get('view-customer/{id}', 'Admin\CustomerController@getViewCustomer');
            });
            
            Route::group(['prefix' => 'service-booking'], function () {
                Route::get('get-service-list', 'Admin\ServiceBookingController@getBookedServiceList');
                Route::get('booked-service-list-ajax', 'Admin\ServiceBookingController@getBookedServiceListAjax');
                Route::get('disputed-service-list', 'Admin\ServiceBookingController@getDisputedServiceList');
                Route::get('disputed-service-list-ajax', 'Admin\ServiceBookingController@getDisputedServiceListAjax');
            });
            Route::group(['prefix' => 'revenue'], function () {
                Route::get('revenue-gain', 'Admin\RevenueController@getRevenueGain');
                Route::get('revenue-gain-list-ajax', 'Admin\RevenueController@getRevenueGainListAjax');
                Route::get('booking-ratio', 'Admin\RevenueController@getBookingRatio');
                Route::get('booking-ratio-list-ajax', 'Admin\RevenueController@getBookingRatioListAjax');
                Route::get('used-service-list', 'Admin\RevenueController@getUsedServiceList');
                Route::get('used-service-list-ajax', 'Admin\RevenueController@getUsedServiceListAjax');
                Route::get('upcoming-revenue-list', 'Admin\RevenueController@getUpcomingRevenueList');
                Route::get('upcoming-revenue-list-ajax', 'Admin\RevenueController@getUpcomingRevenueListAjax');
                Route::get('repeated-user-list', 'Admin\RevenueController@getRepeatedUserList');
                Route::get('repeated-user-list-ajax', 'Admin\RevenueController@getRepeatedUserListAjax');
            });
        });
    });


    Route::group(['prefix' => 'beautician'], function () {
        Route::get('/', 'Web\BeauticianController@getHome');
        
        Route::get('signup', 'Web\BeauticianController@getSignup');
        Route::post('signup', 'Web\BeauticianController@postSignup');
        Route::get('signup-success', 'Web\BeauticianController@getSignupSuccess');
        Route::get('waiting-approval', 'Web\BeauticianController@getWaitingScreen');
        Route::get('approval-rejected', 'Web\BeauticianController@getRejectedScreen');
        Route::get('privacy-policy', 'Web\BeauticianController@getPrivacyPolicy');
        Route::get('terms-and-conditions', 'Web\BeauticianController@getTermsAndConditions');
        Route::get('forgot-password', 'Web\BeauticianController@getForgotPassword');
        Route::post('forgot-password', 'Web\BeauticianController@postForgotPassword');
        Route::get('resetPassword', 'Web\BeauticianController@getResetPassword');
        Route::post('reset-password', 'Web\BeauticianController@postResetPassword');
        Route::post('change-password', 'Web\BeauticianController@postChangePassword');
        Route::get('login', 'Web\BeauticianController@getLogin');
        Route::post('login', 'Web\BeauticianController@postLogin');
        Route::get('logout', 'Web\BeauticianController@getLogout');
        
        Route::get('getServiceImages', 'Web\BeauticianController@getServiceImages');
        
         Route::group(['middleware' => ['App\Http\Middleware\AuthBeautician']], function () {
                Route::get('profile', 'Web\BeauticianController@getProfile');
                Route::get('notifications', 'Web\NotificationController@getNotificationsList');
                Route::get('notificationsListAjax', 'Web\NotificationController@getNotificationsListAjax');
                Route::post('saveBeauticianPortfolio', 'Web\BeauticianController@postBeauticianPortfolio');
                Route::get('getPortfolioUpload', 'Web\BeauticianController@getPortfolioUpload');
                Route::get('fixhibitions', 'Web\BeauticianController@fixhibitions');
                Route::get('welcome-screen', 'Web\BeauticianController@getWelcomeScreen');
                Route::post('getAllFixhibition', 'Web\BeauticianController@getAllFixhibition');
                Route::post('getMyFixhibition', 'Web\BeauticianController@getMyFixhibition');
                Route::post('saveBeauticianFixhibition', 'Web\BeauticianController@postBeauticianFixhibition');
                Route::post('deleteFixhibition','Service\BeauticianController@deleteFixhibition');
                Route::post('updateBusinessDescription','Service\BeauticianController@updateBusinessDescription');
                Route::get('getBeauticianPortfolioByService','Web\BeauticianController@getBeauticianPortfolioByService');
                Route::delete('beauticianPortfolio', 'Service\BeauticianController@deleteBeauticianPortfolio');
                Route::post('createService', 'Web\BeauticianController@postCreateService');
                Route::post('updateService', 'Service\BeauticianController@postUpdateService');
                Route::get('getCustomerRating', 'Web\BeauticianController@getCustomerRating');
                Route::get('rateReviewUser', 'Web\BeauticianController@getRateReviewUser');
                Route::post('rateReviewUser', 'Web\BeauticianController@postRateReviewUser');
                Route::group(['prefix' => 'profile'], function () {
                    Route::get('beauticianProfileKit','Web\BeauticianProfileController@getBeauticianProfileKit');
                    Route::get('services','Web\BeauticianProfileController@getServices');
                    Route::delete('deleteService', 'Service\BeauticianController@deleteService');
                    Route::post('saveBeauticianProfileKit','Web\BeauticianProfileController@saveBeauticianProfileKit');
                    Route::delete('deleteBeauticianKit','Web\BeauticianProfileController@deleteBeauticianKit');
                    Route::get('beauticianExpertise','Web\BeauticianProfileController@getBeauticianExpertise');
                    Route::post('saveBeauticianQualificationNSpeciality','Web\BeauticianProfileController@saveBeauticianQualificationNSpeciality');
                    Route::delete('deleteBeauticianExpertise','Web\BeauticianProfileController@deleteBeauticianExpertise');
                });
                Route::group(['prefix' => 'setting'], function () {
                    Route::get('tutorials','Web\BeauticianSettingController@getTutorials');
                    Route::get('editProfile','Web\BeauticianProfileController@getBeauticianProfile');
                    Route::post('beauticanProfile','Web\BeauticianProfileController@postBeauticanProfile');
                    Route::get('latLongByAddress','Web\BeauticianProfileController@getAddressLatLong');
                    Route::get('beauticianAvailability','Web\BeauticianProfileController@getBeauticianAvailability');
                    Route::get('getAvailabilityView','Web\BeauticianProfileController@getAvailabilityView');
                    Route::post('setAvailability','Web\BeauticianProfileController@postSetAvailability');
                    Route::post('getAvailabilityData','Web\BeauticianProfileController@getAvailabilityData');
                    Route::get('checkBooking','Web\BeauticianProfileController@checkBooking');
                    Route::get('getBeauticianAvailableDates','Web\BeauticianProfileController@getBeauticianAvailableDates');
                    Route::get('beauticianPaymentDetail','Web\BeauticianPaymentController@getBeauticianPaymentDetail');
                    Route::post('beauticianPaymentDetail','Web\BeauticianPaymentController@postBeauticianPaymentDetail');
                });

            Route::get('bookings', 'Web\BeauticianBookingController@getBookings');
            Route::get('flagCustomer', 'Web\BeauticianBookingController@getFlagCustomer');
            Route::post('flagCustomer', 'Web\BeauticianBookingController@postFlagCustomer');
            Route::get('booking-details', 'Web\BeauticianBookingController@getBookingDetails');
            Route::get('getCustomerProfile', 'Web\BeauticianBookingController@getCustomerProfile');
            Route::get('raiseDispute', 'Web\BeauticianBookingController@getRaiseDispute');
            Route::post('raiseDispute', 'Service\UserController@raiseDispute');
            Route::post('share-image', 'Web\BeauticianBookingController@postShareImage');
            
            Route::get('getBeauticianCurrentBooking', 'Service\BeauticianController@getBeauticianCurrentBooking');

            Route::post('cancelBooking', 'Service\ServiceBookingController@cancelBooking');

            Route::post('setBeauticianTimeliness', 'Service\NotificationController@setBeauticianTimeliness');


            Route::group(['prefix' => 'notification'], function () {
                            Route::post('markRead', 'Service\NotificationController@markRead');
                            Route::get('new', 'Web\NotificationController@getNewNotificationCount');
                        
                     });
            Route::get('facebook/share-image', 'Web\FacebookController@shareImage');
         });
        Route::get('cron/getDeleteSuspendedUser', 'Web\CronController@getDeleteSuspendedUser');
    });
});

