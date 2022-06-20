<?php

namespace App\Http\Controllers\API;

use App\Model\ReservedRoom;

class ReservedRoomController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "reserved_room",
            "model" => "App\Model\ReservedRoom", 
            "prefixId" => "052"
        ];
    }

    public function getQuery(){
        return ReservedRoom::query();
    }

    public function getModel(){
        return "App\Model\ReservedRoom";
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
