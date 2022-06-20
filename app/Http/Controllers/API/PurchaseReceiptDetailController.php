<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PurchaseReceiptDetail;
use App\Services\PurchaseOrderHandler;
use App\Services\PurchaseReceivingHandler;
use App\Services\PurchaseReceivingDetailHandler;
use App\Exceptions\CustomException;

class PurchaseReceiptDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "pr_details",
            "model" => "App\Model\PurchaseReceiptDetail",
            'prefixId' => 'prd',
        ];
    }

    public function getQuery(){
        return PurchaseReceiptDetail::query();
    }

    public function getModel(){
        return "App\Model\PurchaseReceiptDetail";
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

    public function afterCreate(&$lstNewRecords){
        
        
    }

    public function afterUpdate(&$lstUpdateRecords, $mapOldRecords = []){

        // throw new CustomException("Amount Payment!", $lstUpdateRecords);

        // exit();

        $recordReceiptDetai = [];

        foreach ($lstUpdateRecords as $key => $newRecord) {
            $oldRecord = $mapOldRecords[$newRecord["id"]];

            $total_qty = 0;
            if ($oldRecord["receive_qty"] > $newRecord["receive_qty"]) {
                $total_qty = $newRecord["receive_qty"] - $oldRecord["receive_qty"];
            }

            if ($oldRecord["receive_qty"] < $newRecord["receive_qty"]) {
                $total_qty = $newRecord["receive_qty"] - $oldRecord["receive_qty"];
            }
            
            $newRecord["receive_qty"] = $total_qty;
            $recordReceiptDetai[] = $newRecord;

           
        }
    }
    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){

        // PurchaseReceivingDetailHandler::updateReceiptDetail($lstNewRecords);

    }

    public function afterDelete($lstOldRecords){

    }
}
