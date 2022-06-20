<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Properties;

class PropertyController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'properties',
            'model' => 'App\Model\Properties', 
            'modelTranslate' => 'App\Model\PropertyTranslation',
            'prefixId' => 'prop',
            'prefixLangId' => 'prop0t',
            'parent_id' => 'properties_id'
        ];
    }
    
    public function getQuery(){
        return Properties::query();
    }
    
    public function getModel(){
        return 'App\Model\Properties';
    }   
    
}
