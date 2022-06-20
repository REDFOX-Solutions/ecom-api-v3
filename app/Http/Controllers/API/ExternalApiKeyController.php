<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ExternalApiKey;

class ExternalApiKeyController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "external_api_key",
            "model" => "App\Model\ExternalApiKey", 
            "prefixId" => "EX0"
        ];
    }

    public function getQuery(){
        return ExternalApiKey::query();
    }

    public function getModel(){
        return "App\Model\ExternalApiKey";
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
