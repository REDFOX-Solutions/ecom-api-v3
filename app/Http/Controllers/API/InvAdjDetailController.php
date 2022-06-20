<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\InvAdjDetail;

class InvAdjDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "inv_adj_detail",
            "model" => "App\Model\InvAdjDetail",
            'modelTranslate' => 'App\Model\InvAdjDetailTranslate',
            'prefixId' => 'adjd',
            'prefixLangId' => 'adjd0t',
            'parent_id' => 'inv_adj_detail_id'
        ];
    }

    public function getQuery(){
        return InvAdjDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\InvAdjDetail';
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
