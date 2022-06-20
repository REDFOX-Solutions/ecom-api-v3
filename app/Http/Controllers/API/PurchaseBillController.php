<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PurchaseBill;
use App\Http\Resources\RestResource;
use App\Services\DatabaseGW;
use App\Services\JournalEntryHandler;
use App\Services\PurchaseBillHandler;

class PurchaseBillController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "purchase_bills",
            "model" => "App\Model\PurchaseBill",
            'modelTranslate' => 'App\Model\PurchaseBillTranslation',
            'prefixId' => 'pb',
            'prefixLangId' => 'pb0t',
            'parent_id' => 'purchase_bills_id'
        ];
    }

    public function getQuery(){
        return PurchaseBill::query();
    }

    public function getModel(){
        return "App\Model\PurchaseBill";
    }

    public function getCreateRules(){
        return [
        ];
    }

    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }

    public function beforeCreate(&$listNewRecords)
    {
        //to populate field that user didn't input or not required manual input
        PurchaseBillHandler::setDefaultFieldsValue($listNewRecords);
    }

    public function afterCreate(&$lstNewRecords){       

        foreach ($lstNewRecords as $key => &$newBill) {
            //Recalculate bill
            PurchaseBillHandler::reCalcBill($newBill);

            if(isset($newBill["status"]) && $newBill["status"] == "confirmed"){
                JournalEntryHandler::createJEFromBill($newBill["id"]);
            }
        }

    }

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
        foreach($lstNewRecords as $key => &$newBill){
            $oldBill = $mapOldRecords[$newBill["id"]];

            if(isset($newBill["status"]) && 
                $oldBill["status"] != $newBill["status"] && 
                $newBill["status"] == "confirmed")
            {
                PurchaseBillHandler::doBeforeRelease($newBill, $oldBill);
            }
        }
    }
    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
        foreach($lstNewRecords as $key => &$newBill){
            $oldBill = $mapOldRecords[$newBill["id"]];

            if(isset($newBill["status"]) && 
                $oldBill["status"] != $newBill["status"] && 
                $newBill["status"] == "confirmed")
            {
                JournalEntryHandler::createJEFromBill($newBill["id"]);
            }
        }
    }
    //guest access
    public function publicIndex(Request $request){
        try{
            $lstFilter = $request->all(); 
            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $lstFilter));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }
}
