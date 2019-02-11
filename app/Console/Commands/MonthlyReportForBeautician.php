<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: MonthlyReportForBeautician.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (23/08/2018) 
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Log;

class MonthlyReportForBeautician extends Command {
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
    protected $signature = 'runMonthlyReportForBeauticianCron';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        Log::info('cron started for beautician monthly report');
        $cronController = new \App\Http\Controllers\Web\CronController();
        $cronController->sendMonthlyReportToBeautician();
        Log::info('cron end');
    }

}
