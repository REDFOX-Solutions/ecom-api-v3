<?php

namespace App\Services;
use App\Model\PrintInvoiceHistory;
use Carbon\Carbon;

class PrintInvoiceHisHandler
{
    public static function setDefaultFieldsValue(&$lstPrintInvoice)
    {

        $now = Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME); 
        
        foreach ($lstPrintInvoice as $index => &$printInvoice) {
            $printInvoice["print_datetime"] = $now; 
        }
    }
}
