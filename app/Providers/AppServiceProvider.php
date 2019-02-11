<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: AppServiceProvider.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Validator::extend('validateCustomerPassword', 'App\Http\CustomValidator@validateCustomerPassword');
        Validator::extend('validateImageCount', 'App\Http\CustomValidator@validateImageCount');
        Validator::extend('validateBeauticianPortfolioId', 'App\Http\CustomValidator@validateBeauticianPortfolioId');
        Validator::extend('validateKitName', 'App\Http\CustomValidator@validateKitName');
        Validator::extend('validateBeauticianServiceId', 'App\Http\CustomValidator@validateBeauticianServiceId');
        Validator::extend('validateBeauticianSubServiceId', 'App\Http\CustomValidator@validateBeauticianSubServiceId');
        Validator::extend('validateBeauticianAvailabilityDetail', 'App\Http\CustomValidator@validateBeauticianAvailabilityDetail');
        Validator::extend('validateBeauticianAvailabilityExistence', 'App\Http\CustomValidator@validateBeauticianAvailabilityExistence');

        Validator::extend('validateBookingSlotAvailability', 'App\Http\CustomValidator@validateBookingSlotAvailability');
        Validator::extend('uniqueBeauticianService', 'App\Http\CustomValidator@validateUniqueBeauticianService');
        Validator::extend('validateUserBookingId', 'App\Http\CustomValidator@validateUserBookingId');
        Validator::extend('validateUserRatingPoint', 'App\Http\CustomValidator@validateUserRatingPoint');
        Validator::extend('validateReasonId', 'App\Http\CustomValidator@validateReasonId');
        Validator::extend('validateUserRating', 'App\Http\CustomValidator@validateUserRating');
        Validator::extend('validateSlotOverlap', 'App\Http\CustomValidator@validateSlotOverlap');
        Validator::extend('validateFixhibitionImageCount', 'App\Http\CustomValidator@validateFixhibitionImageCount');
        Validator::extend('validateKitNameDuplicacy', 'App\Http\CustomValidator@validateKitNameDuplicacy');
        Validator::extend('validateQualificationSpecialityName', 'App\Http\CustomValidator@validateQualificationSpecialityName');
        Validator::extend('validateQualificationDuplicacy', 'App\Http\CustomValidator@validateQualificationDuplicacy');
        Validator::extend('validateSpecialityDuplicacy', 'App\Http\CustomValidator@validateSpecialityDuplicacy');
        Validator::extend('bookingId', 'App\Http\CustomValidator@validateBookingId');
        Validator::extend('customerIdForBooking', 'App\Http\CustomValidator@validateCustomerIdForBooking');
        Validator::extend('validateBeauticianServiceIdWithBooking', 'App\Http\CustomValidator@validateBeauticianServiceIdWithBooking');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
