<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Sections;

class SectionsController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'sections',
            'model' => 'App\Model\Sections',
            'modelTranslate' => 'App\Model\SectionTranslation',
            'prefixId' => 'sec',
            'prefixLangId' => 'sec0t',
            'parent_id' => 'sections_id'
        ];
    }
    
    public function getQuery(){
        return Sections::query();
    }
    
    public function getModel(){
        return 'App\Model\Sections';
    } 
     
}
