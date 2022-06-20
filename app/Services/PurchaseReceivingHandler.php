<?php

namespace App\Services;

use App\Http\Controllers\API\PurchaseReceiptController;
use App\Http\Controllers\API\PurchaseReceiptDetailController;
use Illuminate\Database\Eloquent\Model;
use App\Model\PurchaseReceiptDetail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PurchaseReceivingHandler
{
    // public static function setDefaultValue(&$lstSetValue){ 
    //     // setfault status //
    //     foreach ($lstSetValue as $key => &$purchase) {
            
    //         $purchase['status'] = 'hold';
    //     }
        
    // }

    public static function setDefaultFieldsValue(&$lstNewReceived){

        $now = Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME); 

        $authUserId = Auth::check() ? Auth::user()->id : 'Anonymous';

        foreach ($lstNewReceived as $index => &$newPurchaseReceived) {

            //default receipt num
            $newPurchaseReceived["receipt_num"] = (!isset($newPurchaseReceived["receipt_num"]) || empty($newPurchaseReceived["receipt_num"])) ? DatabaseGW::generateReferenceCode('purchase_receipts') : $newPurchaseReceived["receipt_num"];
            // $newPurchaseOrder["receipt_num"] = DatabaseGW::generateReferenceCode('purchase_receipts');
            $newPurchaseReceived["status"] = (!isset($newPurchaseReceived["status"]) || empty($newPurchaseReceived["status"])) ? "hold" : $newPurchaseReceived["status"];
        }
    }

    public static function CreatePurchaseDetail($lsReceipt){
        foreach ($lsReceipt as $index => $receipt) {

            $lstCreate_pr_detail = [];

            if(isset($receipt["po_purchase_receipt"]) && !empty($receipt["po_purchase_receipt"])){

                //   create  po_pr
                $obj_pr_detail = $receipt["po_purchase_receipt"];
                
                // loop data form form relationship

                foreach ($obj_pr_detail as $key => &$subobj_pr_detail) { 

                    $new_pr_detail = [];
                  

                    $order_detail = $subobj_pr_detail["order_details"];
                    foreach ($order_detail as $keys =>  &$order_detail_id ){

                        $new_pr_detail["purchase_receipts_id"] = $receipt["id"];
                        $new_pr_detail["purchase_order_id"] = $subobj_pr_detail["id"];
                        $new_pr_detail["po_detail_id"] = $order_detail_id["id"];
                        
                        //push subobj Pr detail to lstCreatePRD
                        $lstCreate_pr_detail[] = $new_pr_detail;
                    }
                }
                $pr_detailController = new PurchaseReceiptDetailController();
                $Created_pr_detail=$pr_detailController->createLocal($lstCreate_pr_detail);

                
            }
            
        }
    }

    public static function receiptDetail($receiptDetail)
    {
        $controller = new PurchaseReceiptController();
        foreach ($receiptDetail as $key => &$prDetail) {

            $recordOrderDetail = PurchaseReceiptDetail::where('purchase_receipts_id', $prDetail["purchase_receipts_id"])->get()
                                ->groupBy("purchase_receipts_id")
                                ->count();

            if ($recordOrderDetail == 0) {

                $receiptDelete = [
                    "id" => $prDetail["purchase_receipts_id"]
                ];
                
                $controller->delete([$receiptDelete]);
            }
           
        }

    }
    
}