<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PhysicalCount;

class PhysicalCountController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "physical_count",
            "model" => "App\Model\PhysicalCount",
            'modelTranslate' => 'App\Model\PhysicalCountTranslate',
            'prefixId' => 'pc',
            'prefixLangId' => 'pc0t',
            'parent_id' => 'physical_count_id'
        ];
    }

    public function getQuery(){
        return PhysicalCount::query();
    }
    
    public function getModel(){
        return 'App\Model\PhysicalCount';
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
