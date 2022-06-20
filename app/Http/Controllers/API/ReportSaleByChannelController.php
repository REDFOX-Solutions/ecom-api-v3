<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\ReportSaleByChannel;
use Illuminate\Http\Request;

class ReportSaleByChannelController extends RestAPI
{
    //
    public function getTableSetting()
    {
        return [
            'tablename' => 'rpt_sale_by_channel',
            'model' => 'App\Model\ReportSaleByChannel',
            'prefixId' => 'rtp'
        ];
    }

    public function getQuery()
    {
        return ReportSaleByChannel::query();  
    }

    public function getModel()
    {
        return 'App\Model\ReportSaleByChannel';
    }
}
