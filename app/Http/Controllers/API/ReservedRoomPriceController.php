<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ReservedRoomPrice;
use App\Services\ReservedRoomPriceHandler;

class ReservedRoomPriceController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "reserved_room_price",
            "model" => "App\Model\ReservedRoomPrice", 
            "prefixId" => "053"
        ];
    }

    public function getQuery(){
        return ReservedRoomPrice::query();
    }

    public function getModel(){
        return "App\Model\ReservedRoomPrice";
    }
    
    public function getCreateRules(){
        return [];
    }

    public function getUpdateRules(){
        return [
            "id" => "required"
        ];
    }

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){
        ReservedRoomPriceHandler::recalcReservationPrice($lstNewRecords);
    }
 
    public function afterUpdate(&$lstNewRecords, $mapOldRecords=[]){
        ReservedRoomPriceHandler::recalcReservationPrice($lstNewRecords);
    }
}
