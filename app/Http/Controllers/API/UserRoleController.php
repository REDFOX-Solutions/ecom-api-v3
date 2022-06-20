<?php

namespace App\Http\Controllers\API; 
use App\Model\UserRoles; 

class UserRoleController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "database_table_name",
            "model" => "App\Model\UserRoles", 
            "prefixId" => "00E"
        ];
    }

    public function getQuery(){
        return UserRoles::query();
    }

    public function getModel(){
        return "App\Model\UserRoles";
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
}