<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Model\ProjectProperties;
use App\Http\Controllers\Controller;

class ProjectPropertyController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'project_properties',
            'model' => 'App\Model\ProjectProperties',
            'modelTranslate' => 'App\Model\ProjPropTranslate',
            'prefixId' => 'proj',
            'prefixLangId' => 'proj0t',
            'parent_id' => 'project_properties_id'
        ];
    }
    
    public function getQuery(){
        return ProjectProperties::query();
    }
    
    public function getModel(){
        return 'App\Model\ProjectProperties';
    }
}
