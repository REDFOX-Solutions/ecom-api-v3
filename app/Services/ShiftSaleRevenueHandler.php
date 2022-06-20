<?php

namespace App\Services;

use App\Http\Controllers\API\ShiftSaleRevenueController;
use App\Model\Invoices;
use Illuminate\Database\Eloquent\Model;

class ShiftSaleRevenueHandler
{
    /**
     * Method to log Shift Sale Revenue after invoice released
     * 
     */
    public static function logSaleRevenue($lstReleasedInvIds){

        $lstInvs = Invoices::whereIn("id", $lstReleasedInvIds)->get()->toArray();
        $staffShift = StaffShiftHandler::openDayShift();

        $totalInvoiceAmt = 0;
        foreach ($lstInvs as $key => $inv) {
            $totalInvoiceAmt += isset($inv["grand_total"]) ? $inv["grand_total"] : 0;
        }

        $existedShiftAmt = isset($staffShift["amount"]) ? $staffShift["amount"] : 0;
        $staffShift["amount"] = $existedShiftAmt + $totalInvoiceAmt;
        $saleRevController = new ShiftSaleRevenueController();
        $saleRevController->updateLocal([$staffShift]);

        
    }
}
