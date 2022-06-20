<?php

namespace App\Http\Controllers\API;
 
use App\Model\APIRoutePermission;

class APIRoutePermissionController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'api_route_permission',
            'model' => 'App\Model\APIRoutePermission', 
            'prefixId' => 'apiRP'
        ];
    }
    
    public function getQuery(){
        return APIRoutePermission::query();
    }
    
    public function getModel(){
        return 'App\Model\APIRoutePermission';
    } 
}
