<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Model\Tag; 

class TagsController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'tags',
            'model' => 'App\Model\Tag',
            'modelTranslate' => 'App\Model\TagTranslation',
            'prefixId' => 'tag',
            'prefixLangId' => 'tag0t',
            'parent_id' => 'tags_id'
        ];
    }
    
    public function getQuery(){
        return Tag::query();
    }
    
    public function getModel(){
        return 'App\Model\Tag';
    }   
     
}
