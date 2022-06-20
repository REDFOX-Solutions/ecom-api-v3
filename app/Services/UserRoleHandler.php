<?php

namespace App\Services;

use App\Model\UserRoles;

class UserRoleHandler 
{
    
    /**
     * Method to get all roles from parent
     * @param $roleIds  list parent role Id
     * @return all role record under parent
     * @author Sopha PUM
     * @created 10-01-2021
     */
    public static function getAllBelowRole($roleIds){

        $lstBelowRoleIds = UserRoles::whereIn("parent_role_id", $roleIds)
                                    ->get()
                                    ->keyBy('id')
                                    ->keys()
                                    ->all();

        //if it has record, we will continue get its child
        if(isset($lstBelowRoleIds) && !empty($lstBelowRoleIds)){
            $lstBelowRoleIds = self::getAllBelowRole($lstBelowRoleIds);
        }

        return array_merge($roleIds, $lstBelowRoleIds);
    }
}