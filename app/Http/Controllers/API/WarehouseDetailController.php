<?php

namespace App\Http\Controllers\API;

use App\Model\ProductWarehouseDetail;

use function GuzzleHttp\Promise\exception_for;

class WarehouseDetailController extends RestAPI
{
    public function getTableSetting()
    {
        return [
            "tablename" => "warehouses",
            "model" => "App\Model\ProductWarehouseDetail",
            "prefixId" => "wd",
            // "modelTranslate" => "App\Model\model_translate",
            // "prefixLangId" => "table_translate_prefix_id",
            // "parent_id" => "column_parent_table_in_translate"
        ];
    }

    public function getQuery()
    {
        return ProductWarehouseDetail::query();
    }

    public function getModel()
    {
        return "App\Model\ProductWarehouseDetail";
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
