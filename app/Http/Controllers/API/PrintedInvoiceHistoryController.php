<?php

namespace App\Http\Controllers\API;

use App\Model\PrintInvoiceHistory;
use App\Services\PrintInvoiceHisHandler;

class PrintedInvoiceHistoryController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'printed_invoice_history',
            'model' => 'App\Model\PrintInvoiceHistory', 
            'prefixId' => 'hisPI', 
        ];
    }
 
    
    public function getQuery(){
        return PrintInvoiceHistory::query();
    }
    
    public function getModel(){
        return 'App\Model\PrintInvoiceHistory';
    }    

    public function beforeCreate(&$lstNewRecords){

        //to populate field that user didn't input or not required manual input
        PrintInvoiceHisHandler::setDefaultFieldsValue($lstNewRecords); 
    }
    
}
