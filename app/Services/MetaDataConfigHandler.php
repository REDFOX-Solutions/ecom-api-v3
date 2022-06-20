<?php

namespace App\Services;

use App\Http\Controllers\API\MetadataConfigController;
use App\Model\MetaDataConfig;
use Illuminate\Support\Facades\Auth;

class MetaDataConfigHandler 
{
    
    /**
     * Method get setting record
     * @param $settingName | String     setting name to get setting record
     * @return list setting records as array of array
     * @author Sopha Pum
     * @createdDate 29/04/2020
     */
    public static function getSetting($settingName){
        
        return MetaDataConfig::where("name", $settingName)
                        ->where("company_id", (Auth::user() !== null ? Auth::user()->company_id : null))
                        ->get()->toArray();
 
    }

    /**
     * Method get setting records
     * @param $lstSettingNames | Array(String)     list setting name to get setting records
     * @return list setting records as array of array
     * @author Sopha Pum
     * @createdDate 29/04/2020
     */
    public static function getSettings($lstSettingNames){
        return MetaDataConfig::whereIn("name", $lstSettingNames)->get()->toArray();
    }

    /**
     * Method create default operation hour
     * default is from 7AM - 11PM
     */
    public static function createDefaultOperationHour(){
        $companyId = (Auth::user() !== null ? Auth::user()->company_id : null);
        $newOpSHour = [
            "name" => GlobalStaticValue::$OP_START_HOUR_NAME,
            "value" => "7",
            "company_id" => $companyId
        ];
        $newOpEHour = [
            "name" => GlobalStaticValue::$OP_END_HOUR_NAME,
            "value" => "23",
            "company_id" => $companyId
        ];

        $settingController = new MetadataConfigController();
        return $settingController->createLocal([$newOpSHour, $newOpEHour]);
    }

    /**
     * Method to get Company OP hour
     */
    public static function getOPHour(){
        //get business hour
        $lstOPHours = self::getSettings([GlobalStaticValue::$OP_START_HOUR_NAME, GlobalStaticValue::$OP_END_HOUR_NAME]);

        //default start and end operation hour
        $startHour = 7;
        $endHour = 23;

        //if there are no setup operation hour, we will create a default one for it
        //operation hour will has both start time and end time so we will has 2 records
        if(empty($lstOPHours) || count($lstOPHours) < 2){
            $lstOPHours = self::createDefaultOperationHour();
        }

        foreach ($lstOPHours as $index => $settingOP) {
            $startHour = ($settingOP["name"] == GlobalStaticValue::$OP_START_HOUR_NAME) ? (int) $settingOP["value"]: $startHour;
            $endHour = ($settingOP["name"] == GlobalStaticValue::$OP_END_HOUR_NAME) ? (int)$settingOP["value"]: $endHour; 
        }

        return [GlobalStaticValue::$OP_START_HOUR_NAME => $startHour, GlobalStaticValue::$OP_END_HOUR_NAME => $endHour];

    }
}
