<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\COATransactionDetail;
use App\Services\COATransactionHandler;
use Illuminate\Http\Request;

class COATransactionDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'coa_transaction_detail',
            'model' => 'App\Model\COATransactionDetail', 
            'prefixId' => '901'
        ];
    }
    
    public function getQuery(){
        return COATransactionDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\COATransactionDetail';
    } 

    public function getCreateRules(){
        return [
            'coa_id'=>'required',
            'trans_date' => 'required'
        ];
    }

    public function beforeCreate(&$lstNewRecords)
    {
        //check parent transaction summary
        foreach ($lstNewRecords as $key => &$newDetial) {
            COATransactionHandler::checkTransSummary($newDetial);
        }
        

    }

    public function afterCreate(&$lstNewRecords)
    {
        //do recalc transaction summary
        COATransactionHandler::recalcTransSummary($lstNewRecords);
    }

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
        # code logic here ...
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
       
    }
}
