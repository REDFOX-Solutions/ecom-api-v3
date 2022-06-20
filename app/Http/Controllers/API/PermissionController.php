<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Permissions;

class PermissionController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'permissions',
            'model' => 'App\Model\Permissions',
            'prefixId' => 'perm'
        ];
    }
    
    public function getQuery(){
        return Permissions::query();
    }
    
    public function getModel(){
        return 'App\Model\Permissions';
    }
    
    public function getCreateRules(){
        return [
            'name' => 'required'
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }
    
}
