<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: DateTimeUtility.php
 * CodeLibrary/Project: Beauty Junkie
 * Author: Amit
 * CreatedOn: date (12/05/2018) 
 */

namespace App\Utilities;

class DateTimeUtility {

   /**
     * function is used to add minutes in a php datetime string
     * @param string $datetime (Y-m-d H:i:s) , $minutes
     * @return string modified datetime
     */
   public static function addMinutesToDateTime($datetime,$minutes)
   {
        $datetimeObj = new \DateTime($datetime);
        $datetimeObj->add(new \DateInterval('PT' . $minutes . 'M'));

        return $datetimeObj->format('Y-m-d H:i:s');
   }


    /**
     * function is used to convert date time to a given timezone 
     * @param string $utcDateTime (Y-m-d H:i:s) , $timezone ("Asia/Kolkata")
     * @return string datetime
     */
   public static function convertDateTimeToTimezone($utcDateTime,$timezone,$format='Y-m-d H:i:s')
   {
      $datetime = new \DateTime($utcDateTime);
      $timezoneObjLocal = new \DateTimeZone($timezone);
      $datetime->setTimezone($timezoneObjLocal);
      return $datetime->format($format);
   }


   /**
     * function is used to convert a timezone to its UTC + offset 
     * @param string $timezone eg('Asia/Kolkata')
     * @return string offset
     */
  public static function getStandardOffsetUTC($timezone)
  {
      if($timezone == 'UTC') {
          return '';
      } else {
          $timezone = new \DateTimeZone($timezone);
          $transitions = array_slice($timezone->getTransitions(), -3, null, true);

          foreach (array_reverse($transitions, true) as $transition)
          {
              if ($transition['isdst'] == 1)
              {
                  continue;
              }

              return sprintf('%+03d:%02u', $transition['offset'] / 3600, abs($transition['offset']) % 3600 / 60);
          }

          return false;
      }
  }

}



?>
