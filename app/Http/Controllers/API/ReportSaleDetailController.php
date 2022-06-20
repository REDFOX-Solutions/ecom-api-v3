<?php

namespace App\Http\Controllers\API;
 
use App\Model\ReportSaleDetail;

class ReportSaleDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'report_sale_detail',
            'model' => 'App\Model\ReportSaleDetail',
            'prefixId' => '012SOD'
        ];
    }
    
    public function getQuery(){
        return ReportSaleDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\ReportSaleDetail';
    }
}
