<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\KitAssemblyDetail;

class KitAssemblyDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "kit_assembly_detail",
            "model" => "App\Model\KitAssemblyDetail",
            // 'modelTranslate' => 'App\Model\InvAdjDetailTranslate',
            'prefixId' => 'kitAD',
            // 'prefixLangId' => 'adjd0t',
            // 'parent_id' => 'inv_adj_detail_id'
        ];
    }

    public function getQuery(){
        return KitAssemblyDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\KitAssemblyDetail';
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
