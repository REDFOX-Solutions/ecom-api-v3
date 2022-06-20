<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\InvoiceDetailController;
use App\Http\Controllers\API\MetadataConfigController;
use App\Http\Controllers\API\PrintedInvoiceHistoryController;
use App\Http\Controllers\API\RecordTypeController;
use App\Http\Controllers\API\SaleOrderController; 
use App\Http\Controllers\API\ShipmentController;
use App\Http\Controllers\API\ShipmentDetailController;
use App\Http\Controllers\API\ReceiptController;
use App\Http\Controllers\API\InvoiceReceiptController;
use App\Http\Controllers\API\SaleOrderDetailController;
use App\Model\PersonAccount;
use App\Model\MetaDataConfig;
use App\Model\SaleOrder;
use App\Model\SaleOrderDetail;
use App\Model\Shipment;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;

class SaleOrderHandler
{
    private static $CONST_ORDER_NUM = "order_number";
    public static $RECORD_TYP_POS_SALE = "pos_sale";

    public static function setDefaultFieldsValue(&$lstNewOrders){
        $now = Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME); 

        //get default customer to update sales order customer if it didn't specific
        // $defaultCustomer = PersonAccountHandler::getDefaultCustomer();
        $defaultPb = SalePriceHandler::getDefaultPricebook();

        $authUserId = Auth::check() ? Auth::user()->id : 'Anonymous';

        //to generate automatic order number
        // self::addOrderNumber($lstNewOrders);

        foreach ($lstNewOrders as $index => &$newSaleOrder) {
            //default status
            $newSaleOrder["status"] = (!isset($newSaleOrder["status"]) || empty($newSaleOrder["status"])) ? "hold" : $newSaleOrder["status"];

            //sales order always need order date, default now
            $newSaleOrder["order_date"] = (!isset($newSaleOrder["order_date"]) || empty($newSaleOrder["order_date"])) ? $now : $newSaleOrder["order_date"];
            
            //we will default general customer for all orders
            // $newSaleOrder["customer_id"] = (!isset($newSaleOrder["customer_id"]) || empty($newSaleOrder["customer_id"])) ? $defaultCustomer["id"] : $newSaleOrder["customer_id"];

            //default saler is current user
            $newSaleOrder["saler_id"] = (!isset($newSaleOrder["saler_id"]) || empty($newSaleOrder["saler_id"])) ? $authUserId : $newSaleOrder["saler_id"];
            
            //setup transaction date
            $newSaleOrder["transaction_date"] = DatetimeUtils::getSaleTransactionDate();

            $newSaleOrder["order_num"] = DatabaseGW::generateReferenceCode('sales_order');
            
            //setup pricebook
            if(!isset($newSaleOrder["pricebook_id"])){
                $newSaleOrder["pricebook_id"] = $defaultPb["id"];
            }
        }
    }

   

    /**
     * Method to generate order number 
     * @param $lstOrders    array order just created
     * @return void
     */
    public static function addOrderNumber(&$lstOrders){

        //get current receipt number from setting
        $lstSettings = MetaDataConfig::where("name", self::$CONST_ORDER_NUM)->get()->toArray();
        $settingCtrl = new MetadataConfigController();

        $startNum = !empty($lstSettings) ? (int) $lstSettings[0]["value"] : 0;
        $companyId = Auth::check() ? Auth::user()->company_id : null;
 
        foreach ($lstOrders as $key => &$order) { 
            $startNum++;
            $order["order_num"] = $startNum; 
        } 

        //update setting back
        //if there are no existed setting for receipt number, we need to create it
        if(empty($lstSettings)){
            $setting = [];
            $setting["name"] = self::$CONST_ORDER_NUM;
            $setting["value"] = $startNum;
            $setting["company_id"] = $companyId;
            $settingCtrl->createLocal([$setting]);
        }else{
            $lstSettings[0]["value"] = $startNum;
            $settingCtrl->upsertLocal($lstSettings);
        }
    }


    /**
     * Method to update sale order fields before it changed to sale completed
     * @param $newOrder     new sale order that has updated
     * @return void
     * @createdby Sopha Pum 09-03-2021
     */
    public static function updateSOFieldsBeforeChanged2Completed(&$newOrder){
        $now = Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME); 

        $newOrder["checkout_date"] = isset($newOrder["checkout_date"]) ? $newOrder["checkout_date"] : $now;
    }

    
     
    /**
     * Method to create printed invoice history
     * This method will work when printed_inv changed, we will create printed_invoice_his record for track it
     * @param $newOrder     order record that just updated
     * @param @oldOrder     existed order record that we didn't updated
     */
    public static function checkCreatePrintInvHis($newOrder, $oldOrder){

        //if newOrder has printed_inv value and it is changed, we will create printed_invoice_his record for track
        if(isset($newOrder["printed_inv"]) && $oldOrder["printed_inv"] != $newOrder["printed_inv"]){
            $printedInvHisCtrl = new PrintedInvoiceHistoryController();
            $newPrintedInv = [
                "sales_order_id" => $newOrder["id"],
                "sub_total" => $newOrder["sub_total"],
                "ordering" => $newOrder["printed_inv"]
            ];
            $printedInvHisCtrl->createLocal([$newPrintedInv]);
        }
    }

    /**
     * Method for logic checkup Order before update
     * @param $newOrder     Sales Order record that entered new value
     * @param $oldOrder     Existed Sale Order record
     */
    public static function reCalcOrderBeforeUpdate(&$newOrder, $oldOrder){
        self::reCalcOrder($newOrder, false);
    } 

    /**
     * Method to recalculate Order after order item changed or added
     * - sub_total
     * - order_total
     * - ordered_qty
     * - shipped_qty
     * @param mixed $orderId  String order id | object order
     * @param $isUpdate     Boolean true if we want to update sale order
     * @return list sale order
     */
    public static function reCalcOrder(&$order, $isUpdate=true){ 

        $isOrderId = gettype($order) == 'string';

        if($isOrderId){
            $lstOrders = SaleOrder::where("id", $order)->get()->toArray();
            $order = $lstOrders[0];
        }

        $controller = new SaleOrderController();

        //get all order items to recalculate subtotal for order
        $sumDetailAmount = 0;
        $orderTotal = 0;
        $orderedQTY = 0;
        $totalShippedQty = 0;

        //sum amount from order detail
        $lstOrderItems = SaleOrderDetail::where("sales_order_id", $order["id"])->get()->toArray();
        foreach ($lstOrderItems as $key => $value) {
            $qty = empty($value["quantity"]) ? 1 : $value["quantity"];
            $unitprice = empty($value["unit_price"]) ? 0 : $value["unit_price"]; 
            $shippedQty = isset($value["shipped_qty"]) && !is_null($value["shipped_qty"]) ? $value["shipped_qty"]: 0;

            $sumDetailAmount += ($qty * $unitprice); 
            $orderedQTY += $qty;
            $totalShippedQty += $shippedQty;
        }
 
        //discount rate and discount amount has only 1
        //we priority on rate
        $existedDisRate = empty($order["discount_rate"]) ? 0 : $order["discount_rate"]; 
        $order["discount_amount"] = $existedDisRate * $sumDetailAmount;
        $existedDis = empty($order["discount_amount"]) ? 0 : $order["discount_amount"];

        $orderTotal = $sumDetailAmount - $existedDis;

        $order["sub_total"] = $sumDetailAmount;
        $order["order_total"] = $orderTotal;
        $order["ordered_qty"] = $orderedQTY;  
        $order["shipped_qty"] = $totalShippedQty;

        if($isUpdate) return $controller->updateLocal([$order]); 
        return [$order];
    }


    public static function checkOrder2AutoCreatePayment($newOrder){
         

        if ($newOrder["so_type"] == "invoice" || 
            $newOrder["so_type"] == "cash sale" || 
            $newOrder["so_type"] == "pos sale") 
        { 
            $lstReceipts = isset($newOrder["receipts"]) ? $newOrder["receipts"] : [];
            self::autoCreateInvAndPayment($newOrder, $lstReceipts);
        } 
    }

    /**
     * Method to auto create Invoice, Shipment, and Receipts for sale order
     * if so_type = invoice, cash sale, pos sale 
     * @param $existedOrder     Existed Sale order in DB
     * @param $lstReceipts      List Payments
     * @return void
     */
    public static function autoCreateInvAndPayment($existedOrder, $lstReceipts){ 
        //calculate payment amount to base currency for each receipt
        ShopCurrencyHandler::calcReceiptBaseAmount($lstReceipts);

        //We dont allow general customer credit the amount, 
        //so we will check the sale total amount with payment
        $totalPayment = 0;
        foreach ($lstReceipts as $index => $receipt) {
            $totalPayment += isset($receipt["amount_base_currency"]) ? $receipt["amount_base_currency"] : 0;
        }
        if(!isset($existedOrder["customer_id"]) && $totalPayment < $existedOrder["grand_total"] ){
            throw new CustomException("General customer cannot credit!", "404");
        }

        $now = Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME); 
        $isPrepayment = ($existedOrder["channel"] == 'delivery');
 
        //to get open shift for current staff
        $staffShift = StaffShiftHandler::openDayShift(); 
        $lstOrderDetails = $existedOrder["active_order_details"];

        //update sale order status to "sale completed" when it has payment
        $totalSaleOrder = $existedOrder["order_total"]; //get total order to calculate 

        $saleOrderController = new SaleOrderController();
        $existedSaleOrder = [
            "id" => $existedOrder["id"],
            "status" => "sale completed",//this status will update to completed on trigger after invoice amount update
            "staff_shifts_id" => $staffShift["id"],
            "customer_id" => $existedOrder["customer_id"],
            "checked_out" => $now
        ];
        $saleOrderController->updateLocal([$existedSaleOrder]);
                
        $authUserId = Auth::check() ? Auth::user()->id : 'Anonymous';
        $customerId = isset($existedOrder["customer_id"]) ? $existedOrder["customer_id"] : null;
        
        //create invoice for pos sale after status=open
        $invCtler = new InvoiceController();
        $inv = [
            "sub_total" => isset($existedOrder["order_total"]) ? $existedOrder["order_total"] : 0,
            "bill_to_id" => $customerId, 
            "grand_total" => isset($existedOrder["order_total"]) ? $existedOrder["order_total"] : 0, 
            "status" => $isPrepayment ? "open": "released", // this will change to "completed" after we create receipt for this invoice
            "amount_paid" => 0, //first create invoice doesn't has amount paid, it will has when receipt created
            "inv_date" => $now
        ]; 
        $lstCreatedInvs = $invCtler->createLocal([$inv]); 
        $newInvId = $lstCreatedInvs[0]["id"];

        //In Business logic we need to create shipment before invoice, but in here we create invoice before shipment
        //because we avoid update it again
        //create shipping with status = "open" (this shipping will update to completed after receipt has completed by the process trigger)
        $shipmentCtrler = new ShipmentController();
        $shipping = [
            "ship_to_id" => $customerId,  
            "ship_by_id" => $authUserId, 
            "invoices_id" => $newInvId,
            "received_by_id" => $customerId, 
            "ship_datetime" => $now, 
            "status" => "confirmed", //(this shipping will update to completed after receipt has completed by the process trigger)
            "total_qty" => $existedOrder["ordered_qty"]//for pos sale, it will completed shipping at order level
        ];
        $lstShipmentsCreated = $shipmentCtrler->createLocal([$shipping]);

        //create shipment detail follow the sale order details
        //create Invoice details from each order details
        $lstNewShipDetails = [];
        $lstNewInvDetails = [];
        $lstUpdateOrderDetails = [];
        foreach ($lstOrderDetails as $index => $orderDetail) {
            $newShipDetail = [
                "shipments_id"          => $lstShipmentsCreated[0]["id"],
                "sales_order_id"        => $existedOrder["id"],
                "sale_order_details_id" => $orderDetail["id"],
                "ship_qty"              => $orderDetail["quantity"],
                "uom_id"                => $orderDetail["uom_id"],
                "is_direct_create"      => 1
            ];
            $lstNewShipDetails[] = $newShipDetail;

            $newInvDetail = [
                "invoices_id"   => $newInvId, 
                "qty"           => $orderDetail["quantity"], 
                "unit_price"    => $orderDetail["unit_price"], 
                "cost"          => $orderDetail["unit_cost"], 
                "products_id"   => $orderDetail["products_id"],
                "service_date"  => $orderDetail["service_date"],
                "discount_amount" => $orderDetail["discount_amount"],
                "discount_rate" => $orderDetail["discount_rate"],
                "shipment_id"   => $lstShipmentsCreated[0]["id"]
            ];
            $lstNewInvDetails[] = $newInvDetail;

            $orderDetail["shipped_qty"] = $orderDetail["quantity"];
            $lstUpdateOrderDetails[] = $orderDetail;
        }

        if(!empty($lstNewInvDetails)){
            $invDetailController = new InvoiceDetailController();
            $invDetailController->createLocal($lstNewInvDetails);
        }
        if(!empty($lstNewShipDetails)){
            $shipDetailController = new ShipmentDetailController();
            $shipDetailController->createLocal($lstNewShipDetails);
        }
        if(!empty($lstUpdateOrderDetails)){
            $orderDetailController = new SaleOrderDetailController();
            $orderDetailController->updateLocal($lstUpdateOrderDetails);
        }

        //If there are no list receipts, it means customer full credit
        if(!is_null($lstReceipts) && count($lstReceipts) > 0 && $existedOrder["so_type"] != "invoice" ){
            //create receipt for each payment method
            //lstReceipts are present number of payment method that user has paid
            $lstCreateReceipts = [];
            $totalAmountPaid = $totalSaleOrder; 
            foreach ($lstReceipts as $key => $receipt) {

                $receiptAmount = isset($receipt["amount_base_currency"]) ? $receipt["amount_base_currency"]: 0;
                $newReceipt = [ 
                    "amount" => $receipt["amount"], 
                    // "payment_method_id" => isset($receipt["payment_method_id"]) ? $receipt["payment_method_id"] : null,
                    "received_from_id" => $customerId,
                    "received_by_id" => $authUserId,
                    "receipt_date" => $now,
                    "cash_account_id" => $receipt["cash_account_id"],
                    "status" => ($isPrepayment ? "open": "closed"),
                    "amount_base_currency" => $receipt["amount_base_currency"],
                    "receipt_type" => ($isPrepayment ? "prepayment" : "payment"),
                    //default comment
                    // "langs" => [
                    //     "en" => [
                    //         "lang_code" => "en",
                    //         "comments" => ""
                    //     ]
                    // ]
                ];

                if(isset($receipt["langs"])){
                    $newReceipt["langs"] = $receipt["langs"];
                }else{

                }
                $totalAmountPaid -= $receiptAmount;
                $lstCreateReceipts[] = $newReceipt;
            }

            $lstCreatedReceipts = [];
            if(isset($lstCreateReceipts) && !empty($lstCreateReceipts)){
                $receiptCtrler = new ReceiptController();
                $lstCreatedReceipts = $receiptCtrler->createLocal($lstCreateReceipts);
            }
            
            //For prepayment, we just update receipt id to sale order
            if($isPrepayment){
                $receiptIds = collect($lstCreatedReceipts)->keyBy("id")->keys()->all();
                $existedSaleOrder["prepayment_ids"] = implode(",", $receiptIds); 
                
            }else{
                //create invoice receipt for each receipt
                //those receipt will has the same invoice because in POS sale & Cash Sale, it will has only 1 invoice
                $lstCreateInvReceipt = [];
                foreach ($lstCreatedReceipts as $key => $newReceipt) {
                    $newInvRecp = [
                        "invoices_id" => $newInvId, 
                        "receipts_id" => $newReceipt["id"], 
                        "amount" => isset($newReceipt["amount_base_currency"]) ? $newReceipt["amount_base_currency"] : 0
                    ];
                    $lstCreateInvReceipt[] = $newInvRecp;
                }
                $invRecpController = new InvoiceReceiptController(); 
                $invRecpController->createLocal($lstCreateInvReceipt);
            }
        }
 

        //Do log staff shift will do on trigger after sale completed
        
    }

     
}
