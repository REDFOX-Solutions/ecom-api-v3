<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Applications;

class ApplicationController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'applications',
            'model' => 'App\Model\Applications',
            'prefixId' => 'app',

            'modelTranslate' => 'App\Model\ApplicationTranslation', 
            'prefixLangId' => 'app0t',
            'parent_id' => 'applications_id'
        ];
    }
    
    public function getQuery(){
        return Applications::query();
    }
    
    public function getModel(){
        return 'App\Model\Applications';
    }
    
    public function getCreateRules(){
        return [
            'name' => 'required'
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }
    
}
