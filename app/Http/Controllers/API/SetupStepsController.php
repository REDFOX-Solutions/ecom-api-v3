<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\SetupSteps;
use Illuminate\Http\Request;

class SetupStepsController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'setup_steps',
            'model' => 'App\Model\SetupSteps',
            'prefixId' => 'setup',
            'modelTranslate' => 'App\Model\SetupStepsTranslate', 
            'prefixLangId' => 'setup0t',
            'parent_id' => 'setup_steps_id'
        ];
    }
    
    public function getQuery(){
        return SetupSteps::query();
    }
    
    public function getModel(){
        return 'App\Model\SetupSteps';
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
