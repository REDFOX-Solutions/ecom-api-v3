<?php

namespace App\Http\Controllers\API;
 
use App\Model\PricebookEntryPlaner;

class PricebookEntryPlanerController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "pbe_planer",
            "model" => "App\Model\PricebookEntryPlaner", 
            "prefixId" => "pbep"
        ];
    }

    public function getQuery(){
        return PricebookEntryPlaner::query();
    }

    public function getModel(){
        return "App\Model\PricebookEntryPlaner";
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
