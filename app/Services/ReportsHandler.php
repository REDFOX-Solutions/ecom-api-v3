<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class ReportsHandler extends Model
{
    public static function logSale($saleOrder){
        ReportSaleHandler::logSaleSummary($saleOrder);
    }
}
