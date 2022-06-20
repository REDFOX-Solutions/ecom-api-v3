<?php

namespace App\Http\Controllers\API;

use App\Model\ReportSaleSummary;

class ReportSaleSummaryController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'report_sale_summary',
            'model' => 'App\Model\ReportSaleSummary',
            'prefixId' => '012S0'
        ];
    }
    
    public function getQuery(){
        return ReportSaleSummary::query();
    }
    
    public function getModel(){
        return 'App\Model\ReportSaleSummary';
    }
}
