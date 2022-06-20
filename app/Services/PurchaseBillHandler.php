<?php

namespace App\Services;

use App\Http\Controllers\API\PurchaseBillController;
use App\Http\Controllers\API\PurchaseReceiptController;
use App\Model\GLAccMapping;
use App\Model\PersonAccount;
use Illuminate\Database\Eloquent\Model;
use App\Model\PurchaseBill;
use App\Model\PurchaseBillPayment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PurchaseBillHandler
{
    public static function setDefaultFieldsValue(&$lstNewOrders){

        $now = Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME); 

        $authUserId = Auth::check() ? Auth::user()->id : 'Anonymous';

        foreach ($lstNewOrders as $index => &$newPurchaseOrder) {

            //default receipt num
            $newPurchaseOrder["bill_num"] = (!isset($newPurchaseOrder["bill_num"]) || empty($newPurchaseOrder["bill_num"])) ? DatabaseGW::generateReferenceCode('purchase_bills') : $newPurchaseOrder["bill_num"];
            // $newPurchaseOrder["bill_num"] = DatabaseGW::generateReferenceCode('purchase_bills');

        }
    }
     //** update receiving */
    public static function reCalcBill($newBills){ 
        if(isset($newBills['receiving_id']) && !empty($newBills["receiving_id"])){

            $controller = new PurchaseReceiptController();

            $updateReceive = [
                "id" => $newBills['receiving_id'],
                "purchase_bills_id" => $newBills['id'],
            ];
    
            $controller->updateLocal([$updateReceive]);
        }
    }

    //** update fiel balance in table bill */
    public static function reCalcPayment($biilID){ 
        $controller = new PurchaseBillController();
        $totalAmount = 0;

        $tableBillPayment = PurchaseBillPayment::where('purchase_bills_id', $biilID)->get()->toArray();

        if(isset($tableBillPayment) && !empty($tableBillPayment)){
            foreach ($tableBillPayment as $key => $value) {
                $totalAmount += isset($value["amount"]) ? $value["amount"] : 0;
            }
        }

        $updateBill = [
            "id" => $biilID,
            "balance" => $totalAmount,
        ];
        $controller->updateLocal([$updateBill]);
    }

    //** update fiel balance in table bill */
    public static function updateRecordBill($reCordBill){

        $controller = new PurchaseBillController();
        
        foreach ($reCordBill as $key => &$billPayment) {

            $balance = 0;
            $status_bill = '';
            $tableBill = PurchaseBill::where('id', $billPayment['purchase_bills_id'])->get()->toArray();
            if(isset($tableBill) && !empty($tableBill)){
                foreach ($tableBill as $index => $value) {

                    $balance = $value["balance"] - $billPayment['amount'];

                    if ($balance < $value['total_balance']) {
                        $status_bill = 'confirmed';
                    }
                    if ($balance >= $value['total_balance']) {
                        $status_bill = 'completed';
                    }
                }
            }
    
            $updateBill = [
                "id" => $billPayment['purchase_bills_id'],
                "status" => $status_bill,
                "balance" => $balance,
            ];
            $controller->updateLocal([$updateBill]);
        }
    }
    
    public static function reCalcPaymentBills($reCordBillPayments){

        $controller = new PurchaseBillController();
        
        foreach ($reCordBillPayments as $key => $billPayment) {

            $updateBill = [];

            $updateBill[] = $billPayment["purchase_bills"];

            foreach ($updateBill as $index => $bill) {
                
                if (isset($bill['total_balance']) && isset($bill['balance']) &&
                    $bill['total_balance'] > 0 &&
                    $bill['balance'] > 0 &&
                    $bill['total_balance'] == $bill['balance']
                ) {
                   
                    $billPay = [
                        "id" => $bill["id"],
                        "status" => "completed"
                    ];
    
                    $controller->updateLocal([$billPay]);
                }
               
            }
        }
    }

    //** extends Purchase receipt controller */
    public static function recalBill($updateRecord, $recordBill){
        
        $controller = new PurchaseBillController();
        $total_balance = 0;
        $tableBill = PurchaseBill::where('id', $updateRecord['purchase_bills_id'])->get()->toArray();
        $old_bill = $tableBill[0];
     
        if ($recordBill == "removeBill") {
            if(isset($old_bill["total_balance"]) && !empty($old_bill["total_balance"])){
                $total_balance = $old_bill["total_balance"] - $updateRecord['total_cost'];
            }
        }

        if ($recordBill == "addBill" ) {
           $total_balance = $old_bill["total_balance"] + $updateRecord['total_cost'];
        }

        $updateBill = [
            "id" => $updateRecord['purchase_bills_id'],
            "total_balance" => $total_balance,
        ];
        $controller->updateLocal([$updateBill]);
    }

    /**
     * Method to recalcute any logic before bill change to release
     * @param $newBill      new bill record
     * @created 08-06-2021
     * @author Sopha Pum
     */
    public static function doBeforeRelease(&$newBill, $oldBill){

        //add vendor chart of account to bill, to avoid vendor change chart of acc while we are do payment
        if(isset($newBill["vendor_id"]) || isset($oldBill["vendor_id"])){
            $vendorId = isset($newBill["vendor_id"]) ? $newBill["vendor_id"] : $oldBill["vendor_id"];
            $lstVendors = PersonAccount::where("id", $vendorId)->get()->toArray();
            $vendor = $lstVendors[0];

            $newBill["vendor_coa_id"] = isset($vendor["personal_coa_id"]) ? $vendor["personal_coa_id"] : null;
        }
        
    }
}