<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Model\ReferenceCode;

class ReferenceCodeController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "reference_code",
            "model" => "App\Model\ReferenceCode", 
            "prefixId" => "REF"
        ];
    }

    public function getQuery(){
        return ReferenceCode::query();
    }

    public function getModel(){
        return "App\Model\ReferenceCode";
    }
    
    public function getCreateRules(){
        return [
        ];
    }

    public function getUpdateRules(){
        return [
            "id" => "required"
        ];
    }

    public function beforeCreate(&$lstNewRecords){
        # code logic here ...
    }
 
    public function afterCreate(&$lstNewRecords){
        # code logic here ...
    }

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){
        # code logic here ...
    }
 
    public function afterUpdate(&$lstNewRecords, $mapOldRecords=[]){
        # code logic here ...
    }
}
