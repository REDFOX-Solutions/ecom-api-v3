<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\SaleTransactionCount;

class SaleTransactionCountController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'sale_transaction_count',
            'model' => 'App\Model\SaleTransactionCount', 
            'prefixId' => 's0t0c'
        ];
    }
    
    public function getQuery(){
        return SaleTransactionCount::query();
    }
    
    public function getModel(){
        return 'App\Model\SaleTransactionCount';
    } 
}
