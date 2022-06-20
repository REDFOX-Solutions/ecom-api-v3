<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PhysicalCountDetail;

class PhysicalCountDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "physical_count_detail",
            "model" => "App\Model\PhysicalCountDetail",
            'modelTranslate' => 'App\Model\PhysicalCountDetailTranslate',
            'prefixId' => 'pcd',
            'prefixLangId' => 'pcd0t',
            'parent_id' => 'phycount_detail_id'
        ];
    }

    public function getQuery(){
        return PhysicalCountDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\PhysicalCountDetail';
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
