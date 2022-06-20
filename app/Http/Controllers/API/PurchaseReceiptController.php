<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PurchaseReceipt;
use App\Http\Resources\RestResource;
use App\Services\AccountingHandler;
use App\Services\DatabaseGW;
use App\Services\JournalEntryHandler;
use App\Services\PurchaseReceivingHandler;
use App\Services\PurchaseBillHandler;
use App\Services\PurchaseOrderHandler;
use App\Services\ReceiptProductHandler;

class PurchaseReceiptController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "purchase_receipts",
            "model" => "App\Model\PurchaseReceipt",
            'modelTranslate' => 'App\Model\PurchaseReceiptTranslaton',
            'prefixId' => 'pr',
            'prefixLangId' => 'pr0t',
            'parent_id' => 'purchase_receipts_id'
        ];
    }

    public function getQuery(){
        return PurchaseReceipt::query();
    }

    public function getModel(){
        return "App\Model\PurchaseReceipt";
    }
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    } 
    public function beforeCreate(&$listNewRecords)
    {
        //to populate field that user didn't input or not required manual input
        PurchaseReceivingHandler::setDefaultFieldsValue($listNewRecords);
    }
    
    public function afterCreate(&$lstNewRecords){
        
        PurchaseReceivingHandler::CreatePurchaseDetail($lstNewRecords);

        foreach ($lstNewRecords as $key => $newReceipt){
            if(isset($newReceipt["status"]) && $newReceipt["status"] == 'confirmed'){
                //NOTE: Keep this method at the latest line
                //Method to populate Chart of Account field to Purchase Receipt Detail
                AccountingHandler::populateCOAFieldsAfterReleasedPR($newReceipt["id"]); 

                ReceiptProductHandler::createReceiptProdAfterReleasedReceipt($newReceipt["id"]);
            }
        };
    }

    //** uddate purchase bill on column total balance */
    public function afterUpdate(&$lstUpdateRecords, $mapOldRecords = []){

        foreach ($lstUpdateRecords as $key => $newRecord) {
            $oldRecord = $mapOldRecords[$newRecord["id"]];

            if (empty($newRecord["purchase_bills_id"]) && isset($oldRecord["purchase_bills_id"]) && !empty($oldRecord["total_cost"])) {
                PurchaseBillHandler::recalBill($oldRecord , "removeBill");
            }

            if (isset($newRecord["purchase_bills_id"]) && empty($oldRecord["purchase_bills_id"])) {
                PurchaseBillHandler::recalBill($newRecord, "addBill");
            }

            if (isset($newRecord["status"]) && 
                $oldRecord["status"] != $newRecord["status"] &&
                $newRecord["status"] == "confirmed") 
            {
                PurchaseOrderHandler::updateOrderDetail($newRecord);

                //create Inventory Receipt Product and released it
                ReceiptProductHandler::createReceiptProdAfterReleasedReceipt($newRecord["id"]);
            }

            if (isset($newRecord["status"]) && 
                $oldRecord["status"] != $newRecord["status"] &&
                $newRecord["status"] == "hold" ||  $newRecord["status"] == "open") 
            {
                PurchaseOrderHandler::updatePurchaseOrderDetail($newRecord);
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
