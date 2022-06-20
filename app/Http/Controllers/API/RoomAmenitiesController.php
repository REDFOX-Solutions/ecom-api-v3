<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\RoomAmenities;
use Illuminate\Http\Request;

class RoomAmenitiesController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "room_amenities",
            "model" => "App\Model\RoomAmenities", 
            "prefixId" => "RA001", 
        ];
    }

    public function getQuery(){
        return RoomAmenities::query();
    }

    public function getModel(){
        return "App\Model\RoomAmenities";
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
