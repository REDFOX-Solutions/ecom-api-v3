<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RestResource;
use App\Exceptions\CustomException;
use App\Http\Controllers\ResponseHandler;
use App\Services\DatabaseGW;
use App\Services\UserHandler;
use Illuminate\Support\Facades\Hash;

class UsersController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "users",
            "model" => "App\Model\User",
            "modelTranslate" => "App\Model\UserTranslation",
            "prefixId" => "usr",
            "prefixLangId" => "usr0t",
            "parent_id" => "users_id"
        ];
    }

    public function getQuery(){
        return User::query();
    }

    public function getModel(){
        return "App\Model\User";
    }
    
    public function getCreateRules(){
        return [
            "phone" => "required|unique:users,phone",
            
            "username" => "required|unique:users,username",
            "email" => "unique:users,email",
            "password" => "required"
            
        ];
    }

    public function beforeCreate(&$lstNewRecords){
        $userInfo = Auth::user();
        foreach ($lstNewRecords as $key => &$user) {
            if(!isset($user["company_id"])){
                $user["company_id"] = $userInfo["company_id"];
            }
            
        }
    }
    public function getUpdateRules(){
        return [
            "id" => "required"
        ];
    } 

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){
        UserHandler::setupFieldsBeforeUpdate($lstNewRecords, $mapOldRecords);
    }

       
    
    public function userProfile(){
        $userInfo = Auth::user();
        return new RestResource($userInfo);
    }

    public function changePassword(Request $request){
        $data = $request->all(); 
  
        $userid = isset($data['id']) ? $data['id'] : Auth::user()->id; 

        $user = User::where("id", $userid)->first(); 
        $isRequiredChangePwd = ($user["status"] == 'required new password');
  
        
        //old password and new password are requried
        if(!isset($data["new_password"])){
            throw new CustomException("New password required!", 0);
        }
        if(!isset($data["old_password"]) && !$isRequiredChangePwd){
            throw new CustomException("Old password required!", 0);
        }

        //we are not allow set new password and old password the same
        if(isset($data["old_password"]) && 
            isset($data["new_password"]) && 
            $data["new_password"] === $data["old_password"] && 
            !$isRequiredChangePwd)
        {
            throw new CustomException("New password can not the same old password!", 0);
        }
        
        //if the request has old password, it can be change password from 
        //- Forgot Password
        //if it doesnt has old password but has Auth, it means it is from new setup user  
        $isValid = (isset($data['old_password']) && Hash::check($data['old_password'], $user['password']));

        //we dont need to check old password user if user is required new password
        if($isRequiredChangePwd == true){
            $isValid = true;
        }
        if($isValid){
            // $updateData["id"] = $user['id'];
            // $updateData['password'] = $data['new_password'];  
            // $lstUpdateUser = [$updateData];
            // $lstRecordUpdated = $this->upsertLocal($lstUpdateUser); 
            $user->password = $data['new_password'];
            $user->status = "active";
            $user->save();
            $lstFilters["id"] = $user->id;
            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $lstFilters));
        }else{
            throw new CustomException("Incorrect old password!", 0);
        }

        return ResponseHandler::clientError("Cannot change password. Contact your admin!");
    }
}
