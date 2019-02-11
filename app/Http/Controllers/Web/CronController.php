<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: CronController.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit kumar
 * CreatedOn: date (23/04/2018) 
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Providers\UserServiceProvider;
use App\Providers\BookingServiceProvider;
use Illuminate\Http\Request;
use Mail;
require_once base_path('public/assets/graphlib/phpgraphlib.php');
require_once base_path('public/assets/graphlib/phpgraphlib_pie.php');
class CronController extends BaseController {

    /**
     * function is used to delete user who have been suspended for more than 24 hrs
     * @return type
     */
    public function getDeleteSuspendedUser() {
        $response = UserServiceProvider::deleteSuspendedUser();
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to complete customer booked service whose end datetime is over
     * @return type
     */
    public function completeCustomerBookedService() {
        $response = BookingServiceProvider::completeCustomerBookedService();
        return $this->sendJsonResponse($response);
    }
    
    /**
     * function is used to send monthly report to beautician
     */
    public function sendMonthlyReportToBeautician() {
        ini_set('max_execution_time', 36000);
        $beauticianMonthlyReportDetail = UserServiceProvider::getBeauticianMonthlyReport();
        $pdfFolder = public_path('temp_pdf');
        if (!file_exists($pdfFolder)) {
            mkdir($pdfFolder, 0777, true);
        }
        $i = 1;
        foreach($beauticianMonthlyReportDetail as $value) {

            $mpdf = new \mPDF();
            $mpdf->WriteHTML(\View::make('beautician.monthly-reports')->with('beauticianMonthlyReportDetail', $value)->render());
            $pdfPath = $pdfFolder.'/monthly_report_'.$value['id'].'.pdf';
            $mpdf->Output($pdfPath,'F');
            $subject = 'BeautyJunkie | Monthly Report for '.date('M');
            Mail::send('email.beautician.monthly_report', $value, function($message) use ($pdfPath,$subject, $value) {
                                    $message->to($value['email'])->subject($subject);
                                    $message->from(env('MAIL_USERNAME'), 'BeautyJunkie');
                                    $message->attach($pdfPath);
                                });

            $i++;
        }
        exec('rm -rf '.$pdfFolder);
    }
    
    /**
     * function is used to generate beautician completed service graph
     * @param Request $request
     */
    public function generateBeauticianCompletedServiceGraph(Request $request) {
        
        $id = $request->get('id');
        $arrBeauticianReport = UserServiceProvider::generateBeauticianCompletedServiceGraph($id);
        $arrData = [];
        foreach($arrBeauticianReport as $value) {
            $arrData[$value->name] = $value->total_service;
        }
        if(count($arrData)>0) {
            $graph = new \PHPGraphLibPie(400, 200);
            array("CBS" => 6.3, "NBC" => 4.5,"FOX" => 2.8, 
                    "ABC" => 2.7, "CW" => 1.4);
            $graph->addData($arrData);
            $graph->setTitle('Completed Services');
            $graph->setLabelTextColor('50, 50, 50');
            $graph->setLegendTextColor('50, 50, 50');
            ob_clean();
            $graph->createGraph();
        }
    }
    
    /**
     * function is used to generate beautician rating graph
     * @param Request $request
     */
    public function generateBeauticianRatingGraph(Request $request) {
        $id = $request->get('id');
        
        $arrBeauticianReport = UserServiceProvider::generateBeauticianRatingGraph($id);
        $arrRating = [];
        for($i=1; $i<=12; $i++) {
            foreach($arrBeauticianReport as $value) {
                if($value->monthly == $i) {
                    $arrRating[$i] = $value->avg_rating;
                }
            }
            if(empty($arrRating[$i])) {
                $arrRating[$i] = 0;
            }
        }
        ob_clean();
        $graph = new \PHPGraphLib(500, 350);
        array(12124, 5535, 43373, 22223, 90432, 23332, 15544, 24523, 32778, 
                38878, 28787, 33243, 34832, 32302);
        $graph->addData($arrRating);
        $graph->setTitle('Average Rating Per Month');
        $graph->setGradient('red', 'maroon');
        
        $graph->createGraph();
    }
}
