<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\HotelAmenities;
use Illuminate\Http\Request;

class HotelAmenitiesController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "hotel_amenities",
            "model" => "App\Model\HotelAmenities", 
            "prefixId" => "HA001", 
            "modelTranslate" => "App\Model\HotelAmenitiesTranslate",
            "prefixLangId" => "HA0010t",
            "parent_id" => "hotel_amenities_id"
        ];
    }

    public function getQuery(){
        return HotelAmenities::query();
    }

    public function getModel(){
        return "App\Model\HotelAmenities";
    }
    
    public function getCreateRules(){
        return [
        ];
    }

    public function getUpdateRules(){
        return [
            "id" => "required", 
        ];
    }

    public function beforeCreate(&$lstNewRecords){
        # code logic here ...
    }
 
    public function afterCreate(&$lstNewRecords){
        # code logic here ...
    }

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){
        # code logic here ...
    }
 
    public function afterUpdate(&$lstNewRecords, $mapOldRecords=[]){
        # code logic here ...
    }
}
