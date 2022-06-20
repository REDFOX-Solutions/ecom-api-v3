<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\AppPermission;

class AppPermissionController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'app_permission',
            'model' => 'App\Model\AppPermission',
            'prefixId' => 'appP'
        ];
    }
    
    public function getQuery(){
        return AppPermission::query();
    }
    
    public function getModel(){
        return 'App\Model\AppPermission';
    }
    
    public function getCreateRules(){
        return [
            'app_id' => 'required',
            'permission_id' => 'required'
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }
    
}
