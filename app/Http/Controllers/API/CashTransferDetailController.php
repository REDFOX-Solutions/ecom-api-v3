<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\CashTransferDetail;

class CashTransferDetailController extends RestAPI
{
    //
    protected function getQuery()
    {
        return CashTransferDetail::query();
    }
    protected function getModel()
    {
        return 'App\Model\CashTransferDetail';
    }

    protected function getTableSetting()
    {
        return [
            'tablename' => 'cash_transfer_details',
            'model' => 'App\Model\CashTransferDetail',
            'prefixId' => 'ctfd'
        ];
    }
}
