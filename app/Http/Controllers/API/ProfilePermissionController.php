<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\ProfilePermission;
use Illuminate\Http\Request;

class ProfilePermissionController extends RestAPI
{
    
    public function getTableSetting(){
        return [
            'tablename' => 'profile_permission',
            'model' => 'App\Model\ProfilePermission', 
            'prefixId' => '00eP'
        ];
    }
    
    public function getQuery(){
        return ProfilePermission::query();
    }
    
    public function getModel(){
        return 'App\Model\ProfilePermission';
    }   
}
