<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\ReportSaleByItem;
use Illuminate\Http\Request;

class ReportSaleByItemController extends RestAPI
{
    //
    public function getTableSetting()
    {
        return [
            'tablename' => 'rpt_sale_by_item',
            'model' => 'App\Model\ReportSaleByItem',
            'prefixId' => 'rptSOI'
        ];
    }

    public function getQuery()
    {
        return ReportSaleByItem::query();  
   }

    public function getModel()
    {
        return 'App\Model\ReportSaleByItem';
    }
}
