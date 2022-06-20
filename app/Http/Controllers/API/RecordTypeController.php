<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\RecordType;

class RecordTypeController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'record_type',
            'model' => 'App\Model\RecordType',
            'prefixId' => '012'
        ];
    }
    
    public function getQuery(){
        return RecordType::query();
    }
    
    public function getModel(){
        return 'App\Model\RecordType';
    }
    
    public function getCreateRules(){
        return [
            
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    } 
}
