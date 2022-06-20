<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\InvoiceReceipt;
use App\Services\InvoiceReceiptHandler;

class InvoiceReceiptController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'invoice_receipt',
            'model' => 'App\Model\InvoiceReceipt', 
            'prefixId' => 'invD'
        ];
    }
    
    public function getQuery(){
        return InvoiceReceipt::query();
    }
    
    public function getModel(){
        return 'App\Model\InvoiceReceipt';
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

    public function beforeCreate(&$lstNewRecords){

    }
    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){

    }
    public function beforeDelete(&$lstNewRecords){

    }

    public function afterCreate(&$lstNewRecords){
        InvoiceReceiptHandler::updateInvs($lstNewRecords);
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords=[]){

    }
    public function afterDelete($lstOldRecords){

    }
}
