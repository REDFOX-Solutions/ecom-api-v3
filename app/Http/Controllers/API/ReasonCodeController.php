<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\ReasonCode;

class ReasonCodeController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "reason_code",
            "model" => "App\Model\ReasonCode",
            'modelTranslate' => 'App\Model\ReasonCodeTranslation',
            'prefixId' => 'rc',
            'prefixLangId' => 'rc0t',
            'parent_id' => 'reason_code_id'
        ];
    }

    public function getQuery(){
        return ReasonCode::query();
    }
    
    public function getModel(){
        return 'App\Model\ReasonCode';
    }
    
    public function getCreateRules(){
        return [ 
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }
}
