<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestResource;
use App\Model\ProfilePermission;
use App\Model\User;
use Illuminate\Http\Request;

class SystemInfoController 
{

    /**
     * Method get related profile by Application Name
     * @param $request      filter request from UI, requried app_id
     * @return List Profile related Application
     * @author Sopha Pum | 20-05-2021
     */
    public function getRelatedProfileApp(Request $request){
        $lstFilter = $request->all(); 

        if(!isset($lstFilter["app_id"])) return response()->json("Required Application!", 404);
        $appId = $lstFilter["app_id"];

        //get all related profile records by profile permission
        $lstProfilePermissions = ProfilePermission::where("object_name", "applications")
                                                    ->where("record_id", $appId)
                                                    ->with("profile")
                                                    ->get()
                                                    ->toArray();

        //filter get only profile record
        $lstProfiles = [];
        foreach ($lstProfilePermissions as $key => $profilePermission) {
            if(isset($profilePermission["profile"]) && !empty($profilePermission["profile"])){
                $lstProfiles[] = $profilePermission["profile"];
            }
        }

        return new RestResource(collect($lstProfiles));
    }

    /**
     * Method get related user by Application Name
     * @param $request      filter request from UI, requried app_id
     * @return List user related Application
     * @author Sopha Pum | 20-05-2021
     */
    public function getRelatedUserApp(Request $request){
        $lstFilter = $request->all(); 

        if(!isset($lstFilter["app_id"])) return response()->json("Required Application!", 404);
        $appId = $lstFilter["app_id"];

        //get all related profile records by profile permission
        $lstProfileIds = ProfilePermission::where("object_name", "applications")
                                        ->where("record_id", $appId) 
                                        ->get()
                                        ->groupBy("profiles_id")
                                        ->keys()
                                        ->all();

        //get all user records related profiles
        $lstUsers = User::whereIn("profile_ids", $lstProfileIds)
                        ->get()
                        ->toArray();

        return new RestResource(collect($lstUsers));
    }
}
