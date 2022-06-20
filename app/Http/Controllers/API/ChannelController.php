<?php

namespace App\Http\Controllers\API;

use App\Model\Channel;

class ChannelController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "channel",
            "model" => "App\Model\Channel", 
            "prefixId" => "chn"
        ];
    }

    public function getQuery(){
        return Channel::query();
    }

    public function getModel(){
        return "App\Model\Channel";
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
