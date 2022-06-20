<?php

namespace App\Http\Controllers\API;
 
use App\Model\APIRoute;

class APIRouteController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'api_routes',
            'model' => 'App\Model\APIRoute', 
            'prefixId' => 'apiR'
        ];
    }
    
    public function getQuery(){
        return APIRoute::query();
    }
    
    public function getModel(){
        return 'App\Model\APIRoute';
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
