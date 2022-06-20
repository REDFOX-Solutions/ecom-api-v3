<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 

class SampleController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "database_table_name",
            "model" => "App\Model\MODEL_NAME",
            "modelTranslate" => "App\Model\MODEL_TRANSLATE",
            "prefixId" => "table_prefix_id",
            "prefixLangId" => "table_translate_prefix_id",
            "parent_id" => "column_parent_table_in_translate"
        ];
    }

    public function getQuery(){
        return SampleModel::query();
    }

    public function getModel(){
        return "App\Model\MODEL_NAME";
    }
    
    public function getCreateRules(){
        return [];
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
