<?php

namespace App\Http\Controllers\API;

use App\Model\StaffShifts;
use Illuminate\Http\Request;
use App\Services\StaffShiftHandler;
use App\Http\Controllers\Controller;
use App\Model\Receipts;
use App\Model\SaleOrder;
use App\Services\DatetimeUtils;
use App\Services\GlobalStaticValue;
use Carbon\Carbon;

class StaffShiftController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'staff_shifts',
            'model' => 'App\Model\StaffShifts', 
            'prefixId' => 's0s'
        ];
    }
    
    public function getQuery(){
        return StaffShifts::query();
    }
    
    public function getModel(){
        return 'App\Model\StaffShifts';
    } 

    public function beforeCreate(&$lstNewShifts){
        StaffShiftHandler::setDefaultFields($lstNewShifts);
    }
    public function afterCreate(&$lstNewRecords)
    {
        // StaffShiftHandler::createCashNote($lstNewRecords); 
    }

    public function endShift(Request $req){

        $lstCashNotes = $req->all(); 
        $lstCreatedCashNotes = [];
        $shiftController = new StaffShiftController();
        $transactionDate = DatetimeUtils::getSaleTransactionDate();

        //get current staff shift for this user
        $lstStaffShifts = StaffShifts::where('transaction_date', $transactionDate)
                                        ->where('owner_id', Auth::user()->id)
                                        ->get()
                                        ->toArray();

        //if there are no any staff shift for that user with transaction date,
        //create a new one for it
        if(empty($lstStaffShifts)){
            $newShift = ['start_datetime' => Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME),
                        'end_datetime' => Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME)];
            $lstNewShifts = [];
            $lstNewShifts[] = $newShift;
            $lstStaffShifts = $shiftController->createLocal($lstNewShifts);
        }
        
        $currentShift = $lstStaffShifts[0];
        
        //if there are not cash note, it means User will create it later
        if(isset($lstCashNotes) && !empty($lstCashNotes)){
            //- Create Cash Note
            //- Create Case Note Details: it will create in cash note after create

            foreach ($lstCashNotes as $index => &$cashNote) {
                $cashNote['staff_shifts_id'] = $currentShift['id']; //add field staff_shifts_id to cashNote
            }
            $cashNoteCtler = new CashNoteController();
            $lstCreatedCashNotes = $cashNoteCtler->createLocal($lstCashNotes);
        }
        
        //- Create Theory Collection
        //- Theory Collection get record from payment receipt
        //  SUM(amount) group by receipts.payment_method_id 
        $lstReceiptToday = Receipts::where('receipt_date', $transactionDate)
                                    ->where('received_by_id', Auth::user()->id)
                                    ->where('receipt_type', 'payment')
                                    ->get()
                                    ->toArray();
        $mapTheoryPaymentMethod = [];
        $totalTheoryCollection = 0;

        foreach ($lstReceiptToday as $index => $receipt) {
            $paymentMethodId = $receipt["payment_method_id"];
            $receiptAmt = isset($receipt["amount"]) ? $receipt["amount"] : 0;

            if(!isset($mapTheoryPaymentMethod[$paymentMethodId])){
                $mapTheoryPaymentMethod[$paymentMethodId] = [
                    "payment_method_id" => $paymentMethodId, 
                    "amount" => 0, 
                    "staff_shifts_id" => $currentShift["id"]
                ];
            }
            $mapTheoryPaymentMethod[$paymentMethodId]["amount"] += $receiptAmt;
            $totalTheoryCollection += $receiptAmt;
        }
        $lstCreateTheories = [];
        foreach ($mapTheoryPaymentMethod as $paymentMethodId => $theory) {
            $lstCreateTheories[] = $theory;
        }

        if(!empty($lstCreateTheories)){
            $theoryController = new TheoryCollectionController();
            $theoryController->createLocal($lstCreateTheories);
        }

        //- Create Sale Count Transaction
        //get record from Order Details group by channel
        $mapSaleTransactionByChannel = [];
        //- Create Sale Revenue 
        //get record from Order Details group by category
        $mapSaleRevByCategory = [];

        $lstCompletedSOs = SaleOrder::where("completed_by_id", Auth::user()->id)
                            ->where("staff_shifts_id", $currentShift["id"])
                            ->whereDate("transaction_date", $transactionDate)
                            ->orderBy("order_date", "asc")
                            ->with('orderDetails')
                            ->get()
                            ->toArray();
        foreach($lstCompletedSOs as $index => $order){
            $lstOrderDetails = isset($order["order_details"]) ? $order["order_details"] : [];
            $channel = $order["channel"];

            //Count for Sale Transaction
            if(!isset($mapSaleTransactionByChannel[$channel])){
                $mapSaleTransactionByChannel[$channel] = [
                    "order_type" => $channel,
                    "qty" => 0,
                    "total_amount" => 0,
                    "staff_shifts_id" => $currentShift["id"]
                ];
            }
            $mapSaleTransactionByChannel[$channel]["qty"] += $order["ordered_qty"];

            
            foreach ($lstOrderDetails as $index => $orderDetail) {
                $categoryId = $orderDetail["product"]["category_ids"];
                $orderDetailAmt = isset($orderDetail["amount"]) ? $orderDetail["amount"]: 0;

                //Sum for Sale Revenue group by category
                if(!isset($mapSaleRevByCategory[$categoryId])){
                    $mapSaleRevByCategory[$categoryId] = [
                        "category_id" => $categoryId,
                        "amount" => 0,
                        "staff_shifts_id" => $currentShift["id"]
                    ];
                }
                $mapSaleRevByCategory[$categoryId]["amount"] += $orderDetailAmt;

            }
        }

        if(!empty($mapSaleTransactionByChannel)){
            $saleTransactionCtrler = new SaleTransactionCountController();
            $saleTransactionCtrler->createLocal(array_values($mapSaleTransactionByChannel));
        }

        if(!empty($mapSaleRevByCategory)){
            $saleRevCtrler = new ShiftSaleRevenueController();
            $saleRevCtrler->createLocal(array_values($mapSaleRevByCategory));
        }
        
        //- Update Staff shift
        $currentShift["end_datetime"] = Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME);
        $currentShift["closed_by_id"] = Auth::user()->id;
        $currentShift["total_theory_collection"] = 0;
        $currentShift["total_actual_collection"] = 0;
        $currentShift["total_discount"] = 0;
        $currentShift["total_sale_rev"] = 0;
        $currentShift["over_short"] = 0;
        $currentShift["transaction_date"] = $transactionDate;
        $currentShift["unbilled_amount"] = 0;
        $lstUpdateShifts = [];
        $lstUpdateShifts[] = $currentShift;
        $shiftController->updateLocal($lstUpdateShifts);

    }
    
}
