<?php

namespace App\Http\Controllers\API;

use App\Model\CostHistory;
use Illuminate\Http\Request;

class CostHistoryController extends RestAPI
{
    public function getTableSetting()
    {
        return [
            "tablename" => "cost_histories",
            "model" => "App\Model\CostHistory",
            "prefixId" => "his",
            // "modelTranslate" => "App\Model\model_translate",
            // "prefixLangId" => "table_translate_prefix_id",
            // "parent_id" => "column_parent_table_in_translate"
        ];
    }

    public function getQuery()
    {
        return CostHistory::query();
    }

    public function getModel()
    {
        return "App\Model\CostHistory";
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
