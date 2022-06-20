<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\RequiredSetup;
use Illuminate\Http\Request;

class RequiredSetupController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'required_setup',
            'model' => 'App\Model\RequiredSetup',
            'prefixId' => 'acur',
            'modelTranslate' => 'App\Model\RequiredSetupTranslate', 
            'prefixLangId' => 'setup',
            'parent_id' => 'required_setup_id'
        ];
    }
    
    public function getQuery(){
        return RequiredSetup::query();
    }
    
    public function getModel(){
        return 'App\Model\RequiredSetup';
    }
    
    public function getCreateRules(){
        return [
            'name' => 'required',
            'ordering' => 'required'
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }
}
