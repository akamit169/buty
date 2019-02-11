<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: ConfirmBeauticianAvailability.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (30/05/2018) 
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Log;
use App\Providers\NotificationServiceProvider;

class ConfirmBeauticianAvailability extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';
    protected $signature = 'confirmBeauticianAvailability';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        Log::info('cron started to send notification to the beautician asking to set the schedule for the next week');
        NotificationServiceProvider::notifyBeauticianToSetAvailabilitySchedule();
        Log::info('cron end');
    }

}
