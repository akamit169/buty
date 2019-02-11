<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: Kernel.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (30/05/2018) 
 */

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

        \App\Console\Commands\RunCron::class,
        \App\Console\Commands\RunCompleteServiceCron::class,
        \App\Console\Commands\RunHoldBookingPaymentCron::class,
        \App\Console\Commands\RunNotifyBeforeBookingCron::class,
        \App\Console\Commands\ConfirmBeauticianOnTime::class,
        \App\Console\Commands\ConfirmBeauticianAvailability::class,
        \App\Console\Commands\DeleteOldNotifications::class,
        \App\Console\Commands\MonthlyReportForBeautician::class

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            \Log::info('Log File generated');
            $fileName = 'laravel-'.date('Y-m-d').'.log';
            if(file_exists($fileName))
            {
             chmod('/var/www/html/beauty/storage/logs/'.$fileName,0777);
            }
            
        })->daily();

        $schedule->command('runcron')->hourly();
        $schedule->command('runcompleteservicecron')->everyMinute()->withoutOverlapping();
        $schedule->command('runholdbookingpaymentcron')->daily();
        $schedule->command('notifyBeforeBooking')->everyThirtyMinutes();
        $schedule->command('confirmBeauticianOnTime')->everyMinute()->withoutOverlapping();
        $schedule->command('confirmBeauticianAvailability')->daily(); 
        $schedule->command('deleteOldNotifications')->daily();
        $schedule->command('runMonthlyReportForBeauticianCron')->monthlyOn(1, '06:00');
    }
}
