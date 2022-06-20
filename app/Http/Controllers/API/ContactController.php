<?php

namespace App\Http\Controllers\API;
 
use App\Model\Contact; 

class ContactController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "contact",
            "model" => "App\Model\Contact",
            "prefixId" => "003",
            "modelTranslate" => "App\Model\ContactTranslate",
            "prefixLangId" => "0030t",
            "parent_id" => "contact_id"
        ];
    }

    public function getQuery(){
        return Contact::query();
    }

    public function getModel(){
        return "App\Model\Contact";
    }
    
    public function getCreateRules(){
        return [];
    }

    public function getUpdateRules(){
        return [
            "id" => "required"
        ];
    } 

}
