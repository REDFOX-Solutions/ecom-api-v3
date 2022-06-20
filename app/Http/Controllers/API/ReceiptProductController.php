<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\ReceiptProduct;
use App\Services\JournalEntryHandler;
use Illuminate\Http\Request;

class ReceiptProductController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'receipt_product',
            'model' => 'App\Model\ReceiptProduct', 
            'prefixId' => '800' ,
            'modelTranslate' => 'App\Model\ReceiptProductTranslate', 
            'prefixLangId' => '800T',
            'parent_id' => 'receipt_product_id'
        ];
    }

    public function getQuery(){
        return ReceiptProduct::query();
    }
    
    public function getModel(){
        return 'App\Model\ReceiptProduct';
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

    public function afterCreate(&$lstNewRecords)
    {
        foreach ($lstNewRecords as $key => $newReceiptProd) {
            if(isset($newReceiptProd["status"]) && $newReceiptProd["status"] == "completed"){
                JournalEntryHandler::createJEFromReceiptProd($newReceiptProd["id"]);
            }
        }
        
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
        foreach ($lstNewRecords as $key => $newReceiptProd) {
            $oldReceiptProd = $mapOldRecords[$newReceiptProd["id"]];

            if(isset($newReceiptProd["status"]) && 
                $oldReceiptProd["status"] != $newReceiptProd["status"] &&
                $newReceiptProd["status"] == 'completed'){

                //if issue change status to completed, we create Journal Entry
                JournalEntryHandler::createJEFromReceiptProd($newReceiptProd["id"]);
            }
        }
    }
}
