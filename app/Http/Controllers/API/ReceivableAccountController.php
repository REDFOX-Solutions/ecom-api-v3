<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\ReceivableAccounts;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ReceivableAccountController;
use App\Services\ReceivableAccountHandler;



class ReceivableAccountController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'receivable_accounts',
            'model' => 'App\Model\ReceivableAccounts',
            'prefixId' => 'racc'
        ];
    }

    public function getQuery(){
        return ReceivableAccounts::query();
    }

    public function getModel(){
        return 'App\Model\ReceivableAccounts';
    }
   public function beforeCreate(&$lstNewRecords){
       ReceivableAccountHandler::setDefault($lstNewRecords);
   }
    public function afterCreate(&$lstNewRecords){
        
        ReceivableAccountHandler::createReceivableDetail($lstNewRecords);
    }
}





    
