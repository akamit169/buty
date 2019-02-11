<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: RunCompleteServiceCron.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (30/05/2018) 
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Log;
use App\Providers\BookingServiceProvider;

class RunCompleteServiceCron extends Command {
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
    protected $signature = 'runcompleteservicecron';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        Log::info('cron started to complete customer booked service');
        BookingServiceProvider::completeCustomerBookedService();
        Log::info('cron end');
    }

}
