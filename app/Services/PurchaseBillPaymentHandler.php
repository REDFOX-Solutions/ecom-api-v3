<?php

namespace App\Services;

use App\Http\Controllers\API\PurchaseBillPaymentController;
use App\Http\Controllers\API\PurchaseBillController;
use Illuminate\Database\Eloquent\Model;
use App\Model\PurchaseBill;

class PurchaseBillPaymentHandler
{
    
    public static function reCalcPayment($newPayment){

        foreach ($newPayment as $key => $arrPayments) {
            $lstPayment=[];

            if(isset($arrPayments["purchase_bills"]) && !empty($arrPayments["purchase_bills"])){

                $purchaseBill = $arrPayments["purchase_bills"];

                foreach ($purchaseBill as $index => &$value) {

                    $billAmount = 0;

                    if ($arrPayments["amount"] >= $value["due_balance"]) {
                        $billAmount = $value["due_balance"];
                        $arrPayments["amount"] -= $billAmount;
                    } else {
                        $billAmount = $arrPayments["amount"];
                    }

                    if (isset($billAmount) && $billAmount > 0) {

                        $billPayment["purchase_payments_id"] = $arrPayments["id"];
                        $billPayment["amount"] = $billAmount;
                        $billPayment["purchase_bills_id"] = $value['id'];

                        $lstPayment[]=$billPayment;
                    }
                }
            }
            $purchasePaymentController = new PurchaseBillPaymentController();
            $Created_pbp = $purchasePaymentController->createLocal($lstPayment);
        }

    }
    public static function reCalcBillsss($updateBill){

        $controller = new PurchaseBillController();

        foreach ($updateBill as $key_bp => &$billPayment) {

            if(isset($billPayment["purchase_bills_id"])){

                $tableBill = PurchaseBill::where('id', $billPayment['purchase_bills_id'])->get()->toArray();
                $billAmount = 0;

                foreach ($tableBill as $key => $value) {
                    
                    if (isset($value["due_balance"])) {
                        if ($billPayment["amount"] >= $value["due_balance"]) {
                            $billAmount = $value["due_balance"];
                            $billPayment["amount"] -= $billAmount;
                        } else {
                            $billAmount = $arrPayments["amount"];
                        }
                    }else{
                        $billAmount = 0;
                    }
                }


                $billRecords = [
                    "id" => $billPayment['purchase_bills_id'],
                    "balance" => $billAmount
                ];

                $controller->updateLocal([$billRecords]);
            }
        }

    }
    
}