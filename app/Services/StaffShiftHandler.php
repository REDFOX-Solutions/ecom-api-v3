<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Http\Controllers\API\CashNoteController; 
use App\Http\Controllers\API\SaleTransactionCountController;
use App\Http\Controllers\API\ShiftSaleRevenueController;
use App\Http\Controllers\API\StaffEndDayController;
use App\Http\Controllers\API\StaffShiftController;
use App\Model\CashNote; 
use App\Model\SaleOrderDetail;
use App\Model\SaleTransactionCount;
use App\Model\ShiftSaleRevenue;
use App\Model\StaffEndDay;
use App\Model\StaffShifts;
use App\Model\TheoryCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StaffShiftHandler{
    /**
    * to do logic related with StaffShifts
    * @createdDate: 06-15-2020
    * @author: phanith
    * @company: redfox web solutions (www.redfox-ws.com) * @changelog:
    */


    /**
     * Method to set default field for StaffShift record before create
     * @author Sopha Pum
     * @createdDate: 18-11-2020
     */
    public static function setDefaultFields(&$lstNewShifts){
        foreach ($lstNewShifts as $index => &$newShift) {
            $newShift["transaction_date"] = !isset($newShift["transaction_date"]) ? DatetimeUtils::getSaleTransactionDate() : $newShift["transaction_date"];
            $newShift["owner_id"] = Auth::user()->id;
        }
    }

    /**
    * purpose of this method create note_cash after create Staffshift success
    * @param PARAM_TYPE new list of StaffShifts * @param $lstNewStaffShifts
    * @createdDate: 06-15-2020
    * @author: phanith
    */
    public static function createCashNote($lstNewStaffShifts){
        $lstCashNotes = []; //new list for store object of note cash 

        foreach ($lstNewStaffShifts as $index => $staffShift) { //extract list to object

            if (isset($staffShift['cash_notes'])) { //check first record has field cash_notes or not

                foreach ($staffShift['cash_notes'] as $index => $cashNote) { 
                    
                    if(isset($cashNote)){
                        $cashNote['staff_shifts_id'] = $staffShift['id']; //add field staff_shifts_id to cashNote
                        $lstCashNotes[] = $cashNote; 

                    }
                }
            }

        }

        if(!empty($lstCashNotes)){ // re-check $lstCashNotes if not empty
            $cashNoteController = new CashNoteController(); 
            $cashNoteController->createLocal($lstCashNotes); //create new cashNotes
        }
    }
 

    /**
     * Method update staff shift record after end day
     * @param $staffEndDay      end day record
     */
    public static function endDayUpdateStaffShift($staffEndDay){

        $endDate = new Carbon($staffEndDay["end_date"]); 

        //get all staff end shift for TODAY and assign staff end day to it
        $lstStaffShift = StaffShifts::whereNull("staff_end_day_id")
                                    ->whereDate("transaction_date", $endDate->toDateString())
                                    ->get()
                                    ->toArray();

        //updated all staff shift with current end day
        $lstStaffShiftIds = [];
        foreach ($lstStaffShift as $index => &$staffShift) {
            $staffShift["staff_end_day_id"] = $staffEndDay["id"];
            $lstStaffShiftIds[] = $staffShift["id"];
        }
        if(count($lstStaffShift) > 0){
            $staffShiftCtrler = new StaffShiftController();
            $staffShiftCtrler->updateLocal($lstStaffShift);
        }
    }


    /**
     * Method to create end day (transaction for TODAY) and 
     * Staff shift (for current user only)
     * @return object staffShift       
     * 
     * @createdDate 09-12-2020
     * @author Sopha Pum
     */
    public static function openDayShift(){
        //staff end day
        $transactionToday = DatetimeUtils::getSaleTransactionDate();
        $currentUser = Auth::user()->id;

        //Check end day for open transaction for today by checking end_date
        $lstTransactionToday = StaffEndDay::whereDate("end_date", $transactionToday)
                                            ->get()
                                            ->toArray();

        //if there are no end day for transaction TODAY yet, we will create a new one
        if(!isset($lstTransactionToday) || empty($lstTransactionToday)){
            $newEndDay = [
                "end_date" => $transactionToday
            ];
            $endDayCtrler = new StaffEndDayController();
            $lstTransactionToday = $endDayCtrler->createLocal([$newEndDay]);
        }
        $enddayToday = $lstTransactionToday[0];

        //check staff shift to create a new one for Current Staff and Transaction TODAY
        $lstExistedShiftToday = StaffShifts::whereDate("transaction_date", $transactionToday)
                                            ->where("owner_id", $currentUser)
                                            ->get()
                                            ->toArray();

        
        //if there are no shift for current user, we will create a new one for current user
        if(!isset($lstExistedShiftToday) || empty($lstExistedShiftToday)){
            $newShift = [
                "start_datetime" => Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME_ISO),    
                "total_theory_collection" => 0, 
                "total_actual_collection" => 0, 
                "total_discount" => 0, 
                "total_sale_rev" => 0, 
                "over_short" => 0, 
                "staff_end_day_id" => $enddayToday["id"],  
                "transaction_date" => $transactionToday, 
                "unbilled_amount" => 0, 
                "owner_id" => $currentUser
            ];

            $shiftController = new StaffShiftController();
            $lstExistedShiftToday = $shiftController->createLocal([$newShift]);
        }

        return $lstExistedShiftToday[0];
    }

    /**
     * Method to log sale order after it completed
     * - We will log  
     *      + Sale Transaction Count -> for staff shift
     *      + Update Sale order shift id
     * @param $lstSaleOrdersCompleted    array completed sale order
     */
    public static function logSaleAfterCompleted($lstSaleOrderIdsCompleted){
 
        
         
        
    }

    /**
     * Method to log shift sake transaction count 
     * @param $orderId                  Sale order id to log revenue
     * @param $staffShiftId             Current StaffShift Id
     * @param $transactionToday         Transaction Today date
     * @return void
     * @author Sopha Pum
     * @createdDate: 01-01-2021
     */
    public static function logSaleRev($orderId, $staffShiftId, $transactionToday){
 
        //sale revenue will has only 1 category
        $mapSaleRev = ShiftSaleRevenue::whereDate("transaction_date", $transactionToday)
                                        ->where("staff_shifts_id", $staffShiftId)
                                        ->get()
                                        ->keyBy("category_id")
                                        ->all();

        $lstOrderDetails = SaleOrderDetail::where("sales_order_id", $orderId)
                                            // ->where() //TODO: filter only active detail
                                            ->with("product")
                                            ->get()
                                            ->toArray(); 

        foreach ($lstOrderDetails as $key => $orderDetail) {
            $product = $orderDetail["product"]; 
            $cateId = $product["default_category_id"];
            $orderDetailAmt = isset($orderDetail["amount"]) ? $orderDetail["amount"] : 0;

            if(!isset($mapSaleRev[$cateId])){

                $newSaleRev = [
                    "category_id" => $cateId, 
                    "amount" => 0, 
                    "total_discount" => 0,
                    "staff_shifts_id" => $staffShiftId, 
                    "transaction_date" => $transactionToday
                ];

                $mapSaleRev[$cateId] = $newSaleRev;
            }

            $mapSaleRev[$cateId]["amount"] += $orderDetailAmt;
            $mapSaleRev[$cateId]["total_discount"] += $orderDetail["discount_amount"];
        }

        
        //do upsert for sale transaction count   
        if(isset($mapSaleRev) && !empty($mapSaleRev)){
            $lstSaleRev = array_values($mapSaleRev);
            $lstSaleRev2Upsert = collect($lstSaleRev)->toArray();
  
            $saleRevCtrler = new ShiftSaleRevenueController();
            $saleRevCtrler->upsertLocal($lstSaleRev2Upsert);
        }
    }

    /**
     * Method to log shift sake transaction count 
     * @param $transactionToday     Transaction Today date
     * @param $staffShift           Current StaffShift
     * @param $lstSaleOrders        List Completed Sale Orders
     * @return void
     * @author Sopha Pum
     * @createdDate: 01-01-2021
     */
    public static function logSaleTransactionCount($transactionToday, $staffShiftId, $saleOrder){
        //get existed sale transaction for TODAY and current staff
        //per day transaction has only 1 channel record
        $mapTransactionCount = SaleTransactionCount::whereDate("transaction_date", $transactionToday)
                                                    ->where("staff_shifts_id", $staffShiftId)
                                                    ->get()
                                                    ->keyBy("channel")
                                                    ->all();


        //get current sale and its details to calculate transaction count 
        $channel = isset($saleOrder["channel"]) ? $saleOrder["channel"] : 'unknown';

        //calculate transaction count
        if(!isset($mapTransactionCount[$channel])){
            $newTransactionCount = [
                "qty" => 0, 
                "total_amount" => 0, 
                "staff_shifts_id" => $staffShiftId, 
                "transaction_date" => $transactionToday, 
                "channel" => $channel
            ];
            $mapTransactionCount[$channel] = $newTransactionCount;
        }
        $mapTransactionCount[$channel]["qty"] += $saleOrder["ordered_qty"];
        $mapTransactionCount[$channel]["total_amount"] += $saleOrder["grand_total"];

        //do upsert for sale transaction count   
        if(isset($mapTransactionCount) && !empty($mapTransactionCount)){
            $lstSaleTrans = array_values($mapTransactionCount);
            $lstSaleTrans2Upsert = collect($lstSaleTrans)->toArray();

            $saleTransacCtrler = new SaleTransactionCountController();
            $saleTransacCtrler->upsertLocal($lstSaleTrans2Upsert);
        }
    }

    /**
     * Method to do recalculate staff shift
     * @param $shiftId      String Staff Shift Id that want to re-calculate
     * @return void
     * @author Sopha Pum
     */
    public static function recalcStaffShift($shiftId, $transactionDate){

        $lstShifts = StaffShifts::where("id", $shiftId)
                                ->whereDate("transaction_date", $transactionDate)
                                ->get()
                                ->toArray();

        if(isset($lstShifts) && !empty($lstShifts)){
            //1 day, 1 shift can have only 1 record
            $shift = $lstShifts[0];

            //get theory collection to recalculate field total_theory_collection 
            //Theory collection is related Receipt
            $lstTheoryCollections = TheoryCollection::where("staff_shifts_id", $shiftId)
                                                    ->whereDate("transaction_date", $transactionDate)
                                                    ->get()
                                                    ->toArray();
            $totalTheoryCollection = 0;
            if(isset($lstTheoryCollections) && !empty($lstTheoryCollections)){
                foreach ($lstTheoryCollections as $key => $theoryCollection) {

                    //TODO: Need to check payment method for cash that need to convert to base curency
                    $theoryAmt = isset($theoryCollection["amount"]) ? $theoryCollection["amount"]: 0;
                    $totalTheoryCollection += $theoryAmt;
                }
            }

            //get actual collection to recalculate field total_actual_collection
            //related cash note manualy entered by user
            $lstCashNotes = CashNote::where("staff_shifts_id", $shiftId)
                                    ->whereDate("transaction_date", $transactionDate)
                                    ->get()
                                    ->toArray();
            $totalCashNote = 0;
            if(isset($lstCashNotes) && !empty($lstCashNotes)){
                foreach ($lstCashNotes as $key => $cashNote) {
                    $cashAmt = isset($cashNote["amount"]) ? $cashNote["amount"]: 0;
                    $totalCashNote += $cashAmt;
                }
            }


            //get Sale revenue to calculate
                //- total_discount
                //- total_sale_rev
            $lstSaleRevenues = ShiftSaleRevenue::where("staff_shifts_id", $shiftId)
                                                ->whereDate("transaction_date", $transactionDate)
                                                ->get()
                                                ->toArray();
            $totalDis = 0;
            $totalSaleRev = 0;
            if(isset($lstSaleRevenues) && !empty($lstSaleRevenues)){
                foreach ($lstSaleRevenues as $index => $saleRev) {
                    $saleAmt = isset($saleRev["amount"]) ? $saleRev["amount"]: 0;
                    $saleDis = isset($saleRev["total_discount"]) ? $saleRev["total_discount"] : 0;

                    $totalDis += $saleDis;
                    $totalSaleRev += $saleAmt;
                }
            }

            //recalculate unbilled_amount
            $shift["total_discount"] = $totalDis;
            $shift["total_sale_rev"] = $totalSaleRev;
            $shift["total_theory_collection"] = $totalTheoryCollection;
            $shift["total_actual_collection"] = $totalCashNote;
            $shift["over_short"] = $shift["total_theory_collection"] - $shift["total_actual_collection"];//calc over_short = theory - actual collection
            // $shift["unbilled_amount"] = ??
            
            $shiftController = new StaffShiftController();
            $shiftController->updateLocal([$shift]);
        }
    }

    /**
     * Method auto log staff shift after sale completed
     * @param $saleOrder    completed sale order
     * @return void
     * @created 20-02-2021
     * @author Sopha Pum
     */
    public static function doLogShift($saleOrder){
        $transactionToday = DatetimeUtils::getSaleTransactionDate();
        $staffShift = self::openDayShift(); 
        self::logSaleRev($saleOrder["id"], $staffShift["id"], $transactionToday);
        self::logSaleTransactionCount($transactionToday, $staffShift["id"], $saleOrder);
        self::recalcStaffShift($staffShift["id"], $transactionToday);
    }

}