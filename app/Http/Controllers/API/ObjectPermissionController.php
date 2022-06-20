<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ObjectPermission;

class ObjectPermissionController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'object_permission',
            'model' => 'App\Model\ObjectPermission', 
            'prefixId' => 'objP'
        ];
    }
    
    public function getQuery(){
        return ObjectPermission::query();
    }
    
    public function getModel(){
        return 'App\Model\ObjectPermission';
    }
    
    public function getCreateRules(){
        return [
            'object_id' => 'required',
            'permission_id' => 'required'
        ];
    }
    
    public function getUpdateRules(){
        return [
        ];
    }
    
}
