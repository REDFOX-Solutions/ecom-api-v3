<?php

namespace App\Services;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\API\PurchasePaymentController;
use App\Model\PurchaseBillPayment;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\CustomException;
use Carbon\Carbon;


class PurchasePaymentHandler{

    public static function setDefaultFieldsValue(&$lstNewOrders){

        $now = Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME); 

        $authUserId = Auth::check() ? Auth::user()->id : 'Anonymous';

        foreach ($lstNewOrders as $index => &$newPurchaseOrder) {

            //default receipt num
            $newPurchaseOrder["payment_num"] = (!isset($newPurchaseOrder["payment_num"]) || empty($newPurchaseOrder["payment_num"])) ? DatabaseGW::generateReferenceCode('purchase_payments') : $newPurchaseOrder["payment_num"];
            
        }
    }

    public static function reCalcPayment($paymentId){

        $controller = new PurchasePaymentController();

        $listBillPayment = PurchaseBillPayment::where("purchase_payments_id", $paymentId)->get()->toArray();
        $amount_payment = 0;
        foreach ($listBillPayment as $key => $valueBillPayment) {
            $amount_payment += $valueBillPayment["amount"];
        }

        //  throw new CustomException("Amount Payment!", $amount_payment);

        $arrPayment = [
            "id" => $paymentId,
            "amount" => $amount_payment
        ];
        $controller->updateLocal([$arrPayment]);
    }
    
    public static function deleteRecordPayment($PaymentRecords){

        $controller = new PurchasePaymentController();

        $controller->delete([$PaymentRecords]);

    }
  
}