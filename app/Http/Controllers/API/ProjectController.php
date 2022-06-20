<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Model\Project;

class ProjectController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'project',
            'model' => 'App\Model\Project',
            'modelTranslate' => 'App\Model\ProjectTranslation',
            'prefixId' => 'proj',
            'prefixLangId' => 'proj0t',
            'parent_id' => 'project_id'
        ];
    }
    
    public function getQuery(){
        return Project::query();
    }
    
    public function getModel(){
        return 'App\Model\Project';
    }
}