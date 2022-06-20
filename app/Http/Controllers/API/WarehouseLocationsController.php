<?php

namespace App\Http\Controllers\API;

use App\Model\WarehouseLocation;
use Illuminate\Http\Request;

class WarehouseLocationsController extends RestAPI
{
    public function getTableSetting()
    {
        return [
            "tablename" => "warehouse_locations",
            "model" => "App\Model\WarehouseLocation",
            "prefixId" => "loc",
            // "modelTranslate" => "App\Model\model_translate",
            // "prefixLangId" => "table_translate_prefix_id",
            // "parent_id" => "column_parent_table_in_translate"
        ];
    }

    public function getQuery()
    {
        return WarehouseLocation::query();
    }

    public function getModel()
    {
        return "App\Model\WarehouseLocation";
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
