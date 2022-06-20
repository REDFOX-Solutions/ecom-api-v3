<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\PayableAccount;
use App\Services\PayableAccountHandler;

class PayableAccountController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'payable_account',
            'model' => 'App\Model\PayableAccount', 
            'prefixId' => 'AP'
        ];
    }
    
    public function getQuery(){
        return PayableAccount::query();
    }
    
    public function getModel(){
        return 'App\Model\PayableAccount';
    }
    public function beforeCreate(&$lstPayable){
        PayableAccountHandler::setDefaultValue($lstPayable);
    }
    public function afterCreate(&$lstPayable){
        // PayableAccountHandler::setAmountValue();

        PayableAccountHandler::createPayableDetail($lstPayable);


    }
}
