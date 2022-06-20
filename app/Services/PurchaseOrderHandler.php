<?php

namespace App\Services;

use App\Http\Controllers\API\PurchaseOrderController;
use App\Http\Controllers\API\PurchaseOrderDetailController; 
use App\Model\PurchaseOrderDetail; 
use App\Model\PurchaseReceiptDetail; 
use App\Model\PurchaseReceipt; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 
use App\Exceptions\CustomException;
class PurchaseOrderHandler
{
    // private static $CONST_ORDER_NUM = "order_number";
    // public static $RECORD_TYP_POS_SALE = "pos_sale";

    public static function setDefaultFieldsValue(&$lstNewOrders){

        $now = Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME); 

        $authUserId = Auth::check() ? Auth::user()->id : 'Anonymous';

        foreach ($lstNewOrders as $index => &$newPurchaseOrder) {

            //default po num
            $newPurchaseOrder["po_num"] = (!isset($newPurchaseOrder["po_num"]) || empty($newPurchaseOrder["po_num"])) ? DatabaseGW::generateReferenceCode('purchase_orders') : $newPurchaseOrder["po_num"];
            // $newPurchaseOrder["po_num"] = DatabaseGW::generateReferenceCode('purchase_orders');

            //default status
            $newPurchaseOrder["status"] = (!isset($newPurchaseOrder["status"]) || empty($newPurchaseOrder["status"])) ? "hold" : $newPurchaseOrder["status"];

        }
    }

    public static function CreatePurchaseDetail($lsPurchase){
        foreach ($lsPurchase as $index => $purchase) {

            $lstCreateOrderDetails = [];

            if(isset($purchase["order_details"]) && !empty($purchase["order_details"])){

                //   create  purchase_detail
                $objPurchaseDetail = $purchase["order_details"];
                
                // loop data form journal_detail form relationship

                foreach ($objPurchaseDetail as $key => &$subobjPurchase) { 

                    $subobjPurchase["purchase_orders_id"]=$purchase["id"];

                    //push subobjPurchase to lstCreateOrderDetails
                    $lstCreateOrderDetails[]=$subobjPurchase;
                }
                $purchaseDetailController=new PurchaseOrderDetailController();
                $CreatedOrder=$purchaseDetailController->createLocal($lstCreateOrderDetails);
    

            }
            
        }
    }
    public static function reCalcOrder($orderId){ 
        $controller = new PurchaseOrderController();

        //get all order items to recalculate subtotal for order
        $sumDetailAmount = 0;
        $orderTotalCost = 0;
        $orderedQTY = 0;

        $lstOrderItems = PurchaseOrderDetail::where("purchase_orders_id", $orderId)->get()->toArray();
        foreach ($lstOrderItems as $key => $value) {
            $qty = empty($value["qty"]) ? 1 : $value["qty"];
            $unitprice = empty($value["cost"]) ? 0 : $value["cost"]; 
            $discount_rate = empty($value["discount_rate"]) ? 0 : $value["discount_rate"]; 

            $sumDetailAmount = ($qty * $unitprice);

            $discount_amount = $sumDetailAmount * $discount_rate;

            $orderTotalCost +=  $sumDetailAmount - $discount_amount;
            $orderedQTY += $qty;
            
        }
        
        //update order
        $order = [
            "id" => $orderId,
            "total_cost" => $orderTotalCost,
            "total_qty" => $orderedQTY
        ];

        $controller->updateLocal([$order]); 
    }

    // extends Purchase Receiving controller //
    public static function updateOrderDetail($udateRecordReceipt){

        $purchaseDetailController = new PurchaseOrderDetailController();
        $receiptDetail = PurchaseReceiptDetail::where('purchase_receipts_id', $udateRecordReceipt["id"])->get()->toArray();

        //** block type return */
       if ($udateRecordReceipt["record_type_name"] =="return") {
            foreach ($receiptDetail as $index => $prDetail) {

                $orderDetail = PurchaseOrderDetail::where('id', $prDetail["po_detail_id"])->get()->toArray();
                $open_qty = 0;
                $in_transit = 0;
                $pr_qty = 0;
                foreach ($orderDetail as $key => $detail) {

                    $pr_qty =  $detail["pr_qty"] - $prDetail["receive_qty"];
                    $in_transit = $detail["in_transit"] + $prDetail["receive_qty"];
                }

                $updateDetail = [
                    "id" => $prDetail["po_detail_id"],
                    "pr_qty" => $pr_qty,
                    "in_transit" => $in_transit
                ];
                $purchaseDetailController->updateLocal([$updateDetail]);
                
            }
       }

       //** block type receipt */
       if ($udateRecordReceipt["record_type_name"] =="receipt") {
            foreach ($receiptDetail as $index => $prDetail) {

                $orderDetail = PurchaseOrderDetail::where('id', $prDetail["po_detail_id"])->get()->toArray();
                $open_qty = 0;
                $in_transit = 0;
                $pr_qty = 0;
                foreach ($orderDetail as $key => $detail) {

                    $pr_qty =  $detail["pr_qty"] + $prDetail["receive_qty"];
                    $open_qty =  $detail["open_qty"] - $prDetail["receive_qty"];
                    $in_transit = $detail["in_transit"] - $prDetail["receive_qty"];
                }

                $updateDetail = [
                    "id" => $prDetail["po_detail_id"],
                    "pr_qty" => $pr_qty,
                    "open_qty" => $open_qty,
                    "in_transit" => $in_transit
                ];
                // throw new CustomException("listRD =>", $updateDetail);
                $purchaseDetailController->updateLocal([$updateDetail]);
                
            }
       }
    }

    // extends purchase receipt controller
    public static function updatePurchaseOrderDetail ($recordReceipt){

        $purchaseDetailController = new PurchaseOrderDetailController();
        $receiptDetail = PurchaseReceiptDetail::where('purchase_receipts_id', $recordReceipt["id"])->get()->toArray();

        //** block type receipt */
        if ($recordReceipt["record_type_name"] =="receipt") {
            if (isset($receiptDetail)) {
                foreach ($receiptDetail as $index => $prDetail) {

                    $orderDetail = PurchaseOrderDetail::where('id', $prDetail["po_detail_id"])->get()->toArray();
                    $in_transit = 0;
                    foreach ($orderDetail as $key => $detail) {
                        if ($recordReceipt["status"] == "hold") {
                            $in_transit = $detail["in_transit"] - $prDetail["receive_qty"];
                        }else{
                            $in_transit = $detail["in_transit"] + $prDetail["receive_qty"];
                        }
                       
                    }
                    $updateDetail = [
                        "id" => $prDetail["po_detail_id"],
                        "in_transit" => $in_transit
                    ];
                    
                    $purchaseDetailController->updateLocal([$updateDetail]);
                }
            }
            
        }

        //** block type return */
        if ($recordReceipt["record_type_name"] =="return") {
            if (isset($receiptDetail)) {
                foreach ($receiptDetail as $index => $prDetail) {

                    $orderDetail = PurchaseOrderDetail::where('id', $prDetail["po_detail_id"])->get()->toArray();
                    $in_transit = 0;
                    $pr_qty = 0;
                    foreach ($orderDetail as $key => $detail) {
                        if ($recordReceipt["status"] == "hold") {
                            $in_transit = $detail["in_transit"] + $prDetail["receive_qty"];
                        }else{
                            $in_transit = $detail["in_transit"] - $prDetail["receive_qty"];
                        }
                    }
                    $updateDetail = [
                        "id" => $prDetail["po_detail_id"],
                        "in_transit" => $in_transit
                    ];
                    
                    $purchaseDetailController->updateLocal([$updateDetail]);
                }
            }
        }

    }
    
}