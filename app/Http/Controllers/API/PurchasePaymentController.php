<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PurchasePayment;
use App\Http\Resources\RestResource;
use App\Services\DatabaseGW;
use App\Services\JournalEntryHandler;
use App\Services\PurchaseBillPaymentHandler;
use App\Services\PurchasePaymentHandler;
use App\Services\PurchaseBillHandler;

class PurchasePaymentController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "purchase_payments",
            "model" => "App\Model\PurchasePayment",
            'modelTranslate' => 'App\Model\PurchasePaymentTranslation',
            'prefixId' => 'pp',
            'prefixLangId' => 'pp0t',
            'parent_id' => 'purchase_payment_id'
        ];
    }

    public function getQuery(){
        return PurchasePayment::query();
    }

    public function getModel(){
        return "App\Model\PurchasePayment";
    }
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    } 
    public function beforeCreate(&$listNewRecords)
    {
        //to populate field that user didn't input or not required manual input
        PurchasePaymentHandler::setDefaultFieldsValue($listNewRecords);
    }
    public function afterCreate(&$lstNewRecords){       

        //Recalculate Payment
        // PurchaseBillPaymentHandler::reCalcPayment($lstNewRecords);

        foreach ($lstNewRecords as $keys => $newPayment) {

            if (isset($newPayment['status']) && 
                    $newPayment['status'] == 'closed') 
            {
                JournalEntryHandler::createJEFromBillPayment($newPayment["id"]);
            }

        }

    }
    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [] ){
        foreach ($lstNewRecords as $keys => $billPaymentRecords) {

            $oldPayment = $mapOldRecords[$billPaymentRecords["id"]];

            if (isset($billPaymentRecords['status']) && 
                $oldPayment["status"] != $billPaymentRecords['status'] && 
                $billPaymentRecords['status'] == 'closed') 
            {
                PurchaseBillHandler::reCalcPaymentBills($billPaymentRecords['purchase_bill_payments']);

                JournalEntryHandler::createJEFromBillPayment($billPaymentRecords["id"]);
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
