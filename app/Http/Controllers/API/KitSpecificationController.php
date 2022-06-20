<?php

namespace App\Http\Controllers\API;
 
use App\Model\KitSpecification; 

class KitSpecificationController extends RestAPI
{
    public function getTableSetting()
    {
        return [
            "tablename" => "kit_specification",
            "model" => "App\Model\KitSpecification",
            "prefixId" => "kit",
            // "modelTranslate" => "App\Model\model_translate",
            // "prefixLangId" => "table_translate_prefix_id",
            // "parent_id" => "column_parent_table_in_translate"
        ];
    }

    public function getQuery()
    {
        return KitSpecification::query();
    }

    public function getModel()
    {
        return "App\Model\KitSpecification";
    }

    public function getCreateRules()
    {
        return [
            // "phone" => "required"
        ];
    }

    public function getUpdateRules()
    {
        return [
            // "id" => "required",
            // "phone" => "numeric|phone_number|max:15"
        ];
    }

    public function beforeCreate(&$lstNewRecords)
    {
        # code logic here ...
    }

    public function afterCreate(&$lstNewRecords)
    {
         

    }

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
        # code logic here ...
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
         
    
    }
}
