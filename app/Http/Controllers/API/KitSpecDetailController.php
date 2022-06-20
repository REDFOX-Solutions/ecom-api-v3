<?php

namespace App\Http\Controllers\API;
use App\Model\KitSpecDetail;

class KitSpecDetailController extends RestAPI
{
    public function getTableSetting()
    {
        return [
            "tablename" => "kit_spec_details",
            "model" => "App\Model\KitSpecDetail",
            "prefixId" => "kitD",
            // "modelTranslate" => "App\Model\model_translate",
            // "prefixLangId" => "table_translate_prefix_id",
            // "parent_id" => "column_parent_table_in_translate"
        ];
    }

    public function getQuery()
    {
        return KitSpecDetail::query();
    }

    public function getModel()
    {
        return "App\Model\KitSpecDetail";
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
        # code logic here ...
    }

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
        # code logic here ...
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
        # code logic here ...
    }
}
