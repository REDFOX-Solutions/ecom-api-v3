<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\SaleOrderPrepayment;

class SaleOrderPrepaymentController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'saleorder_prepayment',
            'model' => 'App\Model\SaleOrderPrepayment', 
            'prefixId' => 'soPrepay'
        ];
    }
    
    public function getQuery(){
        return SaleOrderPrepayment::query();
    }
    
    public function getModel(){
        return 'App\Model\SaleOrderPrepayment';
    }
    
    public function getCreateRules(){
        return [
            'sale_order_id' => 'required',
            "receipt_id" => "required"
        ];
    }
}
