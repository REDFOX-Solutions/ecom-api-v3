<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\PayableAccountDetail;

class PayableAccountDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'payable_account_detail',
            'model' => 'App\Model\PayableAccountDetail', 
            'prefixId' => 'PA0D'
        ];
    }
    
    public function getQuery(){
        return PayableAccountDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\PayableAccountDetail';
    } 
}
