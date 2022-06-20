<?php

namespace App\Services;

use App\Http\Controllers\API\PurchaseBillController;
use Illuminate\Database\Eloquent\Model;
use App\Model\PurchaseBillDetail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PurchaseBillDetailHandler
{
    public static function reCalcBill($bill_id){

        if(isset($bill_id) && !empty($bill_id)){

            $controller = new PurchaseBillController();
            $totalAmount = 0;
            $totalDiscount = 0;

            $tableBillDetail = PurchaseBillDetail::where('bill_id', $bill_id)->get()->toArray();
    
            if(isset($tableBillDetail) && !empty($tableBillDetail)){
                foreach ($tableBillDetail as $key => $value) {

                    $totalDiscount += ($value["qty"] * $value["unit_cost"]) - (isset($value["amount"]) ? $value["amount"] : 0);
                    $totalAmount += isset($value["amount"]) ? $value["amount"] : 0;
                }
            }

            $updateBill = [
                "id" => $bill_id,
                "total_balance" => $totalAmount,
                "total_discount" => $totalDiscount,
            ];
    
            $controller->updateLocal([$updateBill]);
        }
    }
}