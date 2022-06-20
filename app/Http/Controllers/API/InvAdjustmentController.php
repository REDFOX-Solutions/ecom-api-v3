<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\InvAdjustment;

class InvAdjustmentController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "inv_adjustment",
            "model" => "App\Model\InvAdjustment",
            'modelTranslate' => 'App\Model\InvAdjTranslate',
            'prefixId' => 'adj',
            'prefixLangId' => 'adj0t',
            'parent_id' => 'inv_adj_id'
        ];
    }

    public function getQuery(){
        return InvAdjustment::query();
    }
    
    public function getModel(){
        return 'App\Model\InvAdjustment';
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
