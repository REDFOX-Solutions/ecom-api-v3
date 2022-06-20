<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\ReceivableAccountsDetails;

use Illuminate\Http\Request;



class ReceivableAccountDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'receivable_account_details',
            'model' => 'App\Model\ReceivableAccountsDetails',
            'prefixId' => 'raccde'
        ];
    }

    public function getQuery(){
        return ReceivableAccountsDetails::query();
    }

    public function getModel(){
        return 'App\Model\ReceivableAccountsDetails';
    }
}
