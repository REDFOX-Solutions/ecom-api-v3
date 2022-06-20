<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\COATransactionSummary;
use Illuminate\Http\Request;

class COATransactionSummaryController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'coa_transaction_summary',
            'model' => 'App\Model\COATransactionSummary', 
            'prefixId' => '900'
        ];
    }
    
    public function getQuery(){
        return COATransactionSummary::query();
    }
    
    public function getModel(){
        return 'App\Model\COATransactionSummary';
    } 
}
