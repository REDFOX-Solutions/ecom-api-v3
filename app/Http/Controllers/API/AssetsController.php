<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Assets;

class AssetsController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'assets',
            'model' => 'App\Model\Assets',
            'modelTranslate' => 'App\Model\AssetTranslation',
            'prefixId' => 'ast',
            'prefixLangId' => 'ast0t',
            'parent_id' => 'assets_id'
        ];
    }
    
    public function getQuery(){
        return Assets::query();
    }
    
    public function getModel(){
        return 'App\Model\Assets';
    }

    public function getCreateRules(){
        return [ 
            "sections_id" => "required"
        ];
    }
    
}
