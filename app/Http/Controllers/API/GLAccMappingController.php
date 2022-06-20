<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\GLAccMapping;

class GLAccMappingController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'gl_acc_mapping',
            'model' => 'App\Model\GLAccMapping', 
            'prefixId' => 'gl0map'
        ];
    }
    
    public function getQuery(){
        return GLAccMapping::query();
    }
    
    public function getModel(){
        return 'App\Model\GLAccMapping';
    } 
}
