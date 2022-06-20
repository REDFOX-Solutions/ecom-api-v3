<?php

namespace App\Http\Controllers\API;

use App\Exceptions\CustomException;
use App\Model\Warehouse;
use App\Services\WarehouseHandler;
use Exception;
use Illuminate\Http\Request;

use function GuzzleHttp\Promise\exception_for;

class WarehouseController extends RestAPI
{
    public function getTableSetting()
    {
        return [
            "tablename" => "warehouses",
            "model" => "App\Model\Warehouse",
            "prefixId" => "wh",
            // "modelTranslate" => "App\Model\model_translate",
            // "prefixLangId" => "table_translate_prefix_id",
            // "parent_id" => "column_parent_table_in_translate"
        ];
    }

    public function getQuery()
    {
        return Warehouse::query();
    }

    public function getModel()
    {
        return "App\Model\Warehouse";
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
        try {
            $lstRecorded =[];
            foreach ($lstNewRecords as $record) {

                WarehouseHandler::createLocation($lstRecorded, $record);
                WarehouseHandler::createContact($lstRecorded, $record);
                
            }

            // throw new Exception("error..!");
        } catch (Exception $ex) {
            //RollBack Child
            WarehouseHandler::rollBack($lstRecorded);


            //select all id form lstNewRecords to request
            $request = new Request(array_column($lstNewRecords, 'id')); 
            $this->destroys($request); //to Delete
            $lstNewRecords = [];

            throw new CustomException($ex, 500, []);
        }
    }

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
        # code logic here ...
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [])
    {    
        try {

            $lstRecorded = [];
 

            foreach ($lstNewRecords as $record) {

                WarehouseHandler::upsertLocation($lstRecorded, $record);
                WarehouseHandler::upsertContact($lstRecorded, $record);
            }

            // throw new CustomException('error..!', 500, $allErrors = array());
        } catch (Exception $ex) {
            //role back child
            WarehouseHandler::rollBack($lstRecorded);

            //restore to old record
            $this->updateLocal($mapOldRecords);

            $lstNewRecords = [];
            throw new CustomException($ex, 500, []);
        }
    }
}
