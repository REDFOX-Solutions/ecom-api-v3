<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\KitAssembly;

class KitAssemblyController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "kit_assembly",
            "model" => "App\Model\KitAssembly",
            // 'modelTranslate' => 'App\Model\InvAdjDetailTranslate',
            'prefixId' => 'kitA',
            // 'prefixLangId' => 'adjd0t',
            // 'parent_id' => 'inv_adj_detail_id'
        ];
    }

    public function getQuery(){
        return KitAssembly::query();
    }
    
    public function getModel(){
        return 'App\Model\KitAssembly';
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
