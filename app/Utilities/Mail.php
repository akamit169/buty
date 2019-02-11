<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: Mail.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet
 * CreatedOn: date (30/03/2018) 
 */

/*
 * This is an AES Encryption and Decryption Class (128 Bit)
 */

namespace App\Utilities;

class Mail {

  /**
     * Send Mail
     * 
     * @param type $data
     * @return type
     */
    public static function sendMail($data) {
        try {
            
            $user = $data['user'];
            $subject = $data['subject'];
            $status = "";
            $status = \Mail::send($data['view'], $data['data'], function($message) use ($user,$subject) {
                        $message->to($user->email)->subject($subject);
                        $message->from(env('MAIL_USERNAME'), env('APP_NAME', 'BeautyJunkie'));
                    });

            if (count(\Mail::failures()) > 0) {
                $status = false;
            } else {
                $status = true;
            }
            return $status;
        } catch (\Exception $e) {
            throw $e;
        }
    }

}

?>
