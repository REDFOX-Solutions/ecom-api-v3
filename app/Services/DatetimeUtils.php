<?php

namespace App\Services;

use Carbon\Carbon;

class DatetimeUtils 
{
    /**
     * Method to get sale transaction date by shop business hour
     * @return string date  format YYYY-MM-DD 
     */
    public static function getSaleTransactionDate(){

        //get operation hours
        $lstOPHours = MetaDataConfigHandler::getOPHour();
        $sHour = $lstOPHours[GlobalStaticValue::$OP_START_HOUR_NAME];
        $eHour = $lstOPHours[GlobalStaticValue::$OP_END_HOUR_NAME];  
    
        $now = Carbon::now();
        $yesterday = Carbon::now()->addDays(-1);
        $midNight = Carbon::createFromTimeString('23:59:00'); 
        $endHour = Carbon::createFromTimeString($eHour . ':00:00');

        //if start hour < end hour, it means operation hour is in a day
        //e.g.  start=17, end=23 => 5PM-11PM 
        //      start=7,  end=17 => 7AM-5PM
        //if operation is in a day, transaction date will be current today
        //*** the return default TODAY will handle this condition logic

        //if start hour > end hour, it means operation hour is cross a day
        //e.g.  start=17, end=3 => 5PM-3AM
        //if operation is cross a day, we need to check if we should assign TODAY or YESTERDAY as a transaction date
        //the logic is that if the shop open cross day, the cross day is count as Yesterday
        if($sHour > $eHour){
            //if now is less than mid night, it means transaction date isn't cross day so we use TODAY
            //*** the return default TODAY will handle this condition logic

            //if now is grater than mid night and less than end hour, it means transaction is cross day
            //so we need to use Yesterday 
            if($now > $midNight && $now <= $endHour){
                return $yesterday->format(GlobalStaticValue::$FORMAT_DATE); 
            }
        }

        //default TODAY for transaction date
        return $now->format(GlobalStaticValue::$FORMAT_DATE);  
    }
    public static function setPeriod($value){
        if(!isset($value) || empty($value)) return null;
        $fin_period = new Carbon($value); 
        return $fin_period->format(GlobalStaticValue::$FORMAT_FINANCE_PERIOD);
    }
}
