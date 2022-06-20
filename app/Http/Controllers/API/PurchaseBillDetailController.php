<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PurchaseBillDetail; 
use App\Http\Resources\RestResource;
use App\Services\DatabaseGW;
use App\Services\PurchaseBillDetailHandler;

class PurchaseBillDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "purchase_bill_details",
            "model" => "App\Model\PurchaseBillDetail",
            'modelTranslate' => 'App\Model\PurchaseBillDetailTranslation',
            'prefixId' => 'pbd',
            'prefixLangId' => 'pbd0t',
            'parent_id' => 'bill_detail_id'
        ];
    }

    public function getQuery(){
        return PurchaseBillDetail::query();
    }

    public function getModel(){
        return "App\Model\PurchaseBillDetail";
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

        foreach ($lstNewRecords as $key => &$newBillDetail) {
            //Recalculate bill
            PurchaseBillDetailHandler::reCalcBill($newBillDetail["bill_id"]);
        } 
    }
    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [] ){

        foreach ($lstNewRecords as $key => $newBillDetail) {
            PurchaseBillDetailHandler::reCalcBill($newBillDetail['bill_id']);
        } 

    }
    public function afterDelete($lstOldRecords){

        foreach ($lstOldRecords as $key => $updateBill) {
            PurchaseBillDetailHandler::reCalcBill($updateBill['bill_id']);
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
