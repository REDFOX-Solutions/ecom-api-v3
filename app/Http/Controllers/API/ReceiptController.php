<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ResponseHandler;
use App\Model\Receipts; 
use App\Services\ReceiptHandler; 
use Illuminate\Http\Request;

class ReceiptController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'receipts',
            'model' => 'App\Model\Receipts',
            'prefixId' => 'recp',
            'modelTranslate' => 'App\Model\ReceiptTranslation',
            'prefixLangId' => 'recp0t',
            'parent_id' => 'receipts_id'
        ];
    }
    
    public function getQuery(){
        return Receipts::query();
    }
    
    public function getModel(){
        return 'App\Model\Receipts';
    }
    
    public function getCreateRules(){
        return [
            'amount' => 'required'
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }

    public function beforeCreate(&$lstNewRecords){
        //generate receipt number for each receipt
        ReceiptHandler::setDefaultField($lstNewRecords);
    }
    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){

    }
    public function beforeDelete(&$lstNewRecords){

    }

    public function afterCreate(&$lstNewRecords){
    } 

    /**
     * Method to make payment for POS sale
     * @param $request      the POST body
     *                  {
     *                      "sale_order" : {},
     *                      "list_receipts: [
     *                          {
     *                              "amount": "",
     *                              "payment_method_id": "",
     *                              "": "",
     *                          }, 
     *                          {...}
     *                      ]
     *                  }
     */
    public function makePaymentPOSReceipt(Request $request){

        $lstReqObj = $request->all();
        $reqObj = $lstReqObj[0];

        if(isset($reqObj["sale_order"]) && isset($reqObj["list_receipts"])){
            
            $saleOrder = $reqObj["sale_order"]; 
            $lstReceipts = $reqObj["list_receipts"]; 
            
            return ReceiptHandler::makePaymentPOSReceipt($saleOrder, $lstReceipts);
        }else{
            return ResponseHandler::clientError('Invalid Payment!');
        }
        
    }
}
