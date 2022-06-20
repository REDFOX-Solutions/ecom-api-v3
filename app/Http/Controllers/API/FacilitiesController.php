<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\Facilities;
use Illuminate\Http\Request;

class FacilitiesController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "facilities",
            "model" => "App\Model\Facilities", 
            "prefixId" => "F001", 
            "modelTranslate" => "App\Model\FacilitiesTranslate",
            "prefixLangId" => "F0010t",
            "parent_id" => "facilities_id"
        ];
    }

    public function getQuery(){
        return Facilities::query();
    }

    public function getModel(){
        return "App\Model\Facilities";
    }
    
    public function getCreateRules(){
        return [
        ];
    }

    public function getUpdateRules(){
        return [
            "id" => "required", 
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
