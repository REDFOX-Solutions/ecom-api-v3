<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\PurchaseBillPayment;
use Illuminate\Http\Request;
use App\Http\Resources\RestResource;
use App\Services\DatabaseGW;
use App\Services\PurchaseBillHandler;
use App\Services\PurchaseBillPaymentHandler;
use App\Services\PurchasePaymentHandler;

class PurchaseBillPaymentController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "purchase_bill_payments",
            "model" => "App\Model\PurchaseBillPayment",
            'prefixId' => 'pbp'
        ];
    }

    public function getQuery(){
        return PurchaseBillPayment::query();
    }

    public function getModel(){
        return "App\Model\PurchaseBillPayment";
    }
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    } 
    public function afterCreate(&$lstNewRecords){       

        foreach ($lstNewRecords as $key => &$newPayment) {
            //Recalculate bill
            PurchaseBillHandler::reCalcPayment($newPayment['purchase_bills_id']);
            PurchasePaymentHandler::reCalcPayment($newPayment['purchase_payments_id']);
        } 
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [] ){

        foreach ($lstNewRecords as $key => $updatePayment) {
            PurchaseBillHandler::reCalcPayment($updatePayment['purchase_bills_id']);
            PurchasePaymentHandler::reCalcPayment($updatePayment['purchase_payments_id']);
        } 

        //Recalculate bill
        // PurchaseBillPaymentHandler::reCalcBillsss($lstNewRecords);

    }

    public function afterDelete($lstOldRecords){

        // foreach ($lstOldRecords as $key => $updatePayment) {
        //     PurchasePaymentHandler::reCalcPayment($updatePayment['purchase_payments_id']);
        // } 
        
        // foreach ($lstOldRecords as $key => &$newPODetail) {
            //Recalculate bill
            PurchaseBillHandler::updateRecordBill($lstOldRecords);

            // foreach ($lstOldRecords as $key => $payment) {

            //     PurchasePaymentHandler::deleteRecordPayment($payment["purchase_payments_id"]);
            // }
            
        // } 
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
