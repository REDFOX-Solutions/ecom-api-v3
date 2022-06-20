<?php

namespace App\Http\Controllers\API;

use App\Model\PaymentMethod;

class PaymentMethodController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'payment_methods',
            'model' => 'App\Model\PaymentMethod', 
            'prefixId' => 'p0m'
        ];
    }
    
    public function getQuery(){
        return PaymentMethod::query();
    }
    
    public function getModel(){
        return 'App\Model\PaymentMethod';
    } 
}
