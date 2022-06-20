<?php

namespace App\Services;

use App\Model\Company;
use Illuminate\Support\Facades\Auth;

class UserHandler 
{

    public static function setupFieldBeforeCreate(&$lstNewUsers){
        $userInfo = Auth::user();
        $lstCompanies = Company::where("id", $userInfo["company_id"])
                                ->get()
                                ->toArray();

        foreach ($lstNewUsers as $key => &$newUser) {
            $newUser["company_id"] = $userInfo["company_id"];

            //user must required to change password when first create
            $newUser["status"] = 'required new password';

            //get timezone from company if user doesnt choose timezone
            if(!isset($newUser["timezone"])){
                $newUser["timezone"] = isset($lstCompanies[0]["default_timezone"]) ? $lstCompanies[0]["default_timezone"] : null;
            }
        }

         
    }

    public static function setupFieldsBeforeUpdate(&$lstUsers, $mapOldUsers){
        foreach ($lstUsers as $index => &$newUser) {

            //remove forbidden fields
            unset($newUser["password"]);
            unset($newUser["api_token"]);

        }
    }
}
