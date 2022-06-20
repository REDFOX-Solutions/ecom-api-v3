<?php

namespace App\Services;

use App\Http\Controllers\API\ReportSaleDetailController;
use App\Model\ReportSaleDetail;
use App\Model\SaleOrderDetail;
use Carbon\Carbon;

class ReportSaleDetailHandler
{
    
    /**
     * Method to calc sale order detail for report sale detail when sale order change status
     * to completed or from completed to other
     * @param $saleOrder        Sale order record to calculate sale order detail
     * @param $is2Completed     Boolean if sale order is update status to completed
     */
    public static function calcOrderDetail($saleOrder, $is2Completed){
        //get all order detail related sale order to calc report
        $lstOrderDetails = SaleOrderDetail::where("sales_order_id", $saleOrder["id"])->with('product')->get()->toArray();

        $originalOrderDate = $saleOrder["order_date"];
        $orderDate = Carbon::createFromFormat(GlobalStaticValue::$FORMAT_DATETIME, $originalOrderDate)->format('Y-m-d');

        //get existed report to update the calc
        $mapExistedReports = [];
        $lstExistedReportDetail = ReportSaleDetail::where("sale_date", $orderDate)->get()->toArray();
        
        foreach ($lstExistedReportDetail as $key => $reportDetail) {
            $keyDateItemPrice = $reportDetail["sale_date"] . '_' . $reportDetail["product_id"] . '_'. $reportDetail["unit_price"];
            $mapExistedReports[$keyDateItemPrice] = $reportDetail;
        }
 

        foreach ($lstOrderDetails as $index => $orderDetail) {
            $keyDateItemPrice = $orderDate . '_' . $orderDetail["products_id"] . '_' . $orderDetail["unit_price"];

            //if there are no existed key, it means it is new.
            //and we will create temporary field for it
            if(!isset($mapExistedReports[$keyDateItemPrice])){
                $mapExistedReports[$keyDateItemPrice] = [
                    "total_qty" => 0, 
                    "total_amount" => 0, 
                    "total_discount" => 0, 
                    "unit_price" => 0,
                    "product_id" => $orderDetail["products_id"],
                    "category_id" => isset($orderDetail["product"]) && isset($orderDetail["product"]["category_ids"]) ? $orderDetail["product"]["category_ids"] : null
                ]; 
            }

            //get existed value from map
            $existedTotalQty = $mapExistedReports[$keyDateItemPrice]["total_qty"];
            $existedTotalAmt = $mapExistedReports[$keyDateItemPrice]["total_amount"];
            $existedTotalDiscount = $mapExistedReports[$keyDateItemPrice]["total_discount"];

            $qty = isset($orderDetail["quantity"]) ? $orderDetail["quantity"]: 0;
            $unitPrice = isset($orderDetail["unit_price"]) ? $orderDetail["unit_price"]: 0;
            $discount = isset($orderDetail["discount_amount"]) ? $orderDetail["discount_amount"]: 0;
            $amount = $qty * $unitPrice;
            
            //to calc existed with new value
            $newReport = $mapExistedReports[$keyDateItemPrice]; 
            $newReport["total_qty"] = ($is2Completed  == true? ($existedTotalQty + $qty) : ($existedTotalQty - $qty));
            $newReport["total_amount"] = ($is2Completed == true ? ($existedTotalAmt + $amount) : ($existedTotalQty - $qty));
            $newReport["total_discount"] = ($is2Completed == true ? ($existedTotalDiscount + $discount) : ($existedTotalQty - $qty));
            $newReport["unit_price"] = $unitPrice;
            $newReport["sale_date"] = $orderDate;


            //add it to map back for calc next record
            $mapExistedReports[$keyDateItemPrice] = $newReport;
        }

        //to update or create report
        $lst2CreateReports = [];
        $lst2UpdateReports = [];

        
        foreach ($mapExistedReports as $keyDateItemPrice => $newReport) { 

            if(is_object($newReport)){
                $newReport = (array) $newReport;
            }
            if(isset($newReport["id"])){
                $lst2UpdateReports[] = $newReport;
            }else{
                $lst2CreateReports[] = $newReport;
            }
        }  
 
        $reportOrderDetailController = new ReportSaleDetailController();
        if(!empty($lst2CreateReports)){
            $reportOrderDetailController->createLocal($lst2CreateReports);
        }
        if(!empty($lst2UpdateReports)){
            $reportOrderDetailController->updateLocal($lst2UpdateReports);
        }
        return $lst2UpdateReports;
    }

    public static function calcSaleDetailOnCompletedOrder($saleOrder){
        self::calcOrderDetail($saleOrder, true);
    }

    public static function calcSaleDetailUpdateFromCompletedOrder($saleOrder){
        self::calcOrderDetail($saleOrder, false);
    }
}
