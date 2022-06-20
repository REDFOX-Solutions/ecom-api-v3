<?php

namespace App\Http\Controllers\API;

use App\Model\RoomStatus;

class RoomStatusController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "room_status",
            "model" => "App\Model\RoomStatus", 
            "prefixId" => "053"
        ];
    }

    public function getQuery(){
        return RoomStatus::query();
    }

    public function getModel(){
        return "App\Model\RoomStatus";
    }
    
    public function getCreateRules(){
        return [];
    }

    public function getUpdateRules(){
        return [
            "id" => "required"
        ];
    }
}
