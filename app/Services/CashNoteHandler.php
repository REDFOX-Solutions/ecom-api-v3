<?php

namespace App\Services;

use App\Http\Controllers\API\CashNoteDetailController;
use App\Http\Controllers\API\SaleOrderController;
use App\Http\Controllers\API\SaleTransactionCountController;
use App\Http\Controllers\API\ShiftSaleRevenueController;
use App\Http\Controllers\API\StaffShiftController;
use App\Http\Controllers\API\TheoryCollectionController;
use App\Model\Categories;
use App\Model\Products;
use App\Model\SaleOrder;
use App\Model\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CashNoteHandler
{

    /**
     * Method to check and create children record after create Cash Note
     * If there are children record attached in create cash note, we will create it too
     * @param $lstNewCashNotes      List new cash note created
     * @return void
     */
    public static function createChildren($lstNewCashNotes){

        
        foreach ($lstNewCashNotes as $index => $cashNote) { 

            //create cash note details
            if(isset($cashNote["cash_note_details"]) && !empty($cashNote["cash_note_details"])){
                $lstCashNoteDetails = $cashNote["cash_note_details"];

                foreach ($lstCashNoteDetails as $key => $cashDetail) {
                    $cashDetail["cash_note_id"] = $cashNote["id"];
                    $lstCashDetails2Create[] = $cashDetail;
                }

                //create cash note detail after cash note is created
                if(count($lstCashDetails2Create) > 0){
                    $cashDetailController = new CashNoteDetailController();
                    $cashDetailController->createLocal($lstCashDetails2Create);
                }
            }
        }
    }
    public static function doEndShift($lstNewCashNotes){

        //It will has many Cash Note when create so we must get staff_shifts_id to calc and staff end day

        $staffShiftId = '';
        $lstCashDetails2Create = [];

        //generate cash_note_detail record from cash_note by using attribute "cash_note_details"
        foreach ($lstNewCashNotes as $index => $cashNote) {
            $staffShiftId = isset($cashNote["staff_shifts_id"]) ? $cashNote["staff_shifts_id"] : $staffShiftId;

            if(isset($cashNote["cash_note_details"])){
                $lstCashNoteDetails = $cashNote["cash_note_details"];

                foreach ($lstCashNoteDetails as $key => $cashDetail) {
                    $cashDetail["cash_note_id"] = $cashNote["id"];
                    $lstCashDetails2Create[] = $cashDetail;
                }
            }
        }

        if($staffShiftId == '') return;//if there are no staff shift, we dont need to anything more

        //create cash note detail after cash note is created
        if(count($lstCashDetails2Create) > 0){
            $cashDetailController = new CashNoteDetailController();
            $cashDetailController->createLocal($lstCashDetails2Create);
        }

        //get product and map it with category
        $lstActiveProducts = Products::where("is_active", 1)->get()->toArray();
        $mapProdCategories = [];
        foreach ($lstActiveProducts as $index => $product) {
            if(isset($product["category_ids"])){
                $mapProdCategories[$product["id"]] = $product["category_ids"];    
            }
        } 
        $transactionDate = DatetimeUtils::getSaleTransactionDate();

        // - Sale Revenue (shift_sale_revenue)
        // - Get all completed sale orders that completed by user and doesn’t have staff_shift_id value
        //      and matched transaction date
        // - Get all sale order details related sale order above
        $lstCompletedSOs = SaleOrder::where("completed_by_id", Auth::user()->id)
                                    ->whereNull("staff_shifts_id")
                                    ->whereDate("transaction_date", $transactionDate)
                                    ->orderBy("order_date", "asc")
                                    ->with('orderDetails')
                                    ->get()
                                    ->toArray();
        
        // - Sum all items group by category
        $mapSaleTransactionCount = [];
        $mapCategoryTotalAmount = [];
        $totalSaleRevenue = 0;
        $totalDiscount = 0;
        $lstSOIds = []; 

        foreach ($lstCompletedSOs as $index => &$saleOrder) {

            $saleOrder["staff_shifts_id"] = $staffShiftId;//for update sale order staff shift
            $saleOrder["is_end_shift"] = 1;

            $lstSOIds[] = $saleOrder["id"];

            $totalSaleRevenue += isset($saleOrder["order_total"]) ? $saleOrder["order_total"] : 0;
            $totalDiscount += isset($saleOrder["discount_total"]) ? $saleOrder["discount_total"] : 0;

            $orderQty = isset($saleOrder["ordered_qty"]) ? $saleOrder["ordered_qty"] : 0;
            $orderTotal = isset($saleOrder["order_total"]) ? $saleOrder["order_total"] : 0;

            $lstOrderDetails = isset($saleOrder["order_details"]) ? $saleOrder["order_details"] : [];

            foreach ($lstOrderDetails as $index => $orderDetail) {
                $prodId = $orderDetail["products_id"];
                $categoryId = (isset($mapProdCategories[$prodId]) ? $mapProdCategories[$prodId]: 'uncategory');
                $orderDetailAmt = isset($orderDetail["amount"]) ? $orderDetail["amount"] : 0;

                if(!isset($mapCategoryTotalAmount[$categoryId])){
                    $mapCategoryTotalAmount[$categoryId] = 0;
                }

                $mapCategoryTotalAmount[$categoryId] += $orderDetailAmt;
            }

            //to create sale transaction count
            //we group by order type
            $orderTyp = isset($saleOrder["so_type"]) ? $saleOrder["so_type"] : 'dine in';
            if(!isset($mapSaleTransactionCount[$orderTyp])){
                $mapSaleTransactionCount[$orderTyp] = [
                    "order_type" => $orderTyp, 
                    "qty" => 0, 
                    "total_amount" => 0, 
                    "staff_shifts_id" => $staffShiftId
                ];
            }

            // $existedTransaction = $mapSaleTransactionCount[$orderTyp];
            $mapSaleTransactionCount[$orderTyp]["qty"] += $orderQty;
            $mapSaleTransactionCount[$orderTyp]["total_amount"] += $orderTotal;

        }

        if(!empty($lstCompletedSOs)){
            $saleOrderController = new SaleOrderController();
            $saleOrderController->updateLocal($lstCompletedSOs);
        }

        //create sale transaction count
        $lstCreateTransaction = [];
        foreach ($mapSaleTransactionCount as $orderTyp => $transaction) {
            $lstCreateTransaction[] = $transaction;
        }
        if(!empty($lstCreateTransaction)){
            $transactionCtrler = new SaleTransactionCountController();
            $transactionCtrler->createLocal($lstCreateTransaction);
        }

        // - Add result into table shift_sale_revenue
        $lstCreateSaleRev = [];

        foreach ($mapCategoryTotalAmount as $categoryId => $amount) { 
            $newSaleRev = [
                "category_id" => $categoryId,  
                "amount" => $amount, 
                "staff_shifts_id" => $staffShiftId
            ];
            $lstCreateSaleRev[] = $newSaleRev;
        }

        if(count($lstCreateSaleRev) > 0){
            $saleRevCtrler = new ShiftSaleRevenueController();
            $saleRevCtrler->createLocal($lstCreateSaleRev);
        }
        
        // - Total Sale Revenue = SUM(sales_order.sub_total)
        //$totalSaleRevenue

        // - Discount = SUM(sales_order.discount_amount)
        //$totalDiscount

        // - Theory Collection
        //     - Cash = SUM(invoice_receipt.amount) group by receipts.payment_method_id 

        //get all invoice receipt to calc total collection
        //all related payment, we need to calc from receipt
        $mapTheoryPaymentMethod = [];
        $lstShipments = Shipment::whereIn("sales_order_id", $lstSOIds)
                                ->where("status", "completed")
                                ->with("invoice")
                                ->get()
                                ->toArray();
        $totalTheoryCollection = 0;
        if(!empty($lstShipments)){
            foreach ($lstShipments as $index => $shipping) {

                //we count it when shippment has invoice
                if(isset($shipping["invoice"])){
                    $invoice = $shipping["invoice"];

                    $lstInvReceipts = isset($invoice["invoice_receipts"]) ? $invoice["invoice_receipts"] : [];

                    if(!empty($lstInvReceipts)){
                        foreach ($lstInvReceipts as $index => $invRecp) {
                            $recept = isset($invRecp["receipt"]) ? $invRecp["receipt"]: [];
                            $invRecpAmt = isset($invRecp["amount"]) ? $invRecp["amount"]: 0;
                            $paymentMethodId = $recept["payment_method_id"];

                            if(!isset($mapTheoryPaymentMethod[$paymentMethodId])){
                                $mapTheoryPaymentMethod[$paymentMethodId] = [
                                    "payment_method_id" => $paymentMethodId, 
                                    "amount" => 0, 
                                    "staff_shifts_id" => $staffShiftId
                                ];
                            }
 
                            $mapTheoryPaymentMethod[$paymentMethodId]["amount"] += $invRecpAmt;
                            $totalTheoryCollection += $invRecpAmt;
                        }
                    }
                } 
            }

            $lstCreateTheories = [];
            foreach ($mapTheoryPaymentMethod as $paymentMethodId => $theory) {
                $lstCreateTheories[] = $theory;
            }

            if(!empty($lstCreateTheories)){
                $theoryController = new TheoryCollectionController();
                $theoryController->createLocal($lstCreateTheories);
            }
        }
        

        // - Total Theory Collection = SUM(Theory Collection)

        // - Total Actual Collection = Total Sale Revenue - Discount
        $totalActualCollection = $totalSaleRevenue - $totalDiscount;

        // - over_short = Total Actual Collection - Total Theory Collection
        $overShort = $totalActualCollection - $totalTheoryCollection;

        $staffShift = [
            "id" => $staffShiftId,
            //"start_datetime" => $firstSaleDate, 
            "end_datetime" => Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME), 
            "closed_by_id" => Auth::user()->id, 
            "total_theory_collection" => $totalTheoryCollection, 
            "total_actual_collection" => $totalActualCollection, 
            "total_discount" => $totalDiscount, 
            "total_sale_rev" => $totalSaleRevenue, 
            "over_short" => $overShort,  
            "transaction_date" => $transactionDate
        ];
        $staffShiftCtrler = new StaffShiftController();
        $staffShiftCtrler->updateLocal([$staffShift]);
            
    }
}
