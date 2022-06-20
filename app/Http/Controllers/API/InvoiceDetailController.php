<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\InvoiceDetail;
use App\Services\InvoiceHandler;

class InvoiceDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'invoice_details',
            'model' => 'App\Model\InvoiceDetail', 
            'prefixId' => 'invD',
            'modelTranslate' => 'App\Model\InvoiceDetailsTranslation',
            'prefixLangId' => 'invd0t',
            'parent_id' => 'invoice_details_id'
        ];
    }
    
    public function getQuery(){
        return InvoiceDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\InvoiceDetail';
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
        
        if (isset($lstNewRecords) && !empty($lstNewRecords)) {
            $invoiceId = $lstNewRecords[0]['invoices_id'];
            InvoiceHandler::reCalInvoicesTotal($invoiceId);
        }
    }
}
