<?php

namespace App\Services;

use App\Http\Controllers\API\ReportSaleSummaryController;
use App\Model\ReportSaleSummary;
use App\Model\SaleOrder;
use Carbon\Carbon;

class ReportHandler
{
    
    /**
     * Function to summary Sale Order as a report by date
     * @param $oldSaleOrder         existed Sale Order record 
     * @param $newSaleOrder         existed Sale Order record that has changed value
     */
    public static function calcSaleSummaryReport($oldSaleOrder, $newSaleOrder){

        
        //select newSaleOrder back from DB
        $lstNewSaleOrder = SaleOrder::where("id", $newSaleOrder["id"])->get()->toArray();
        $newSaleOrder = $lstNewSaleOrder[0];

        //skip the calc if new order has no status. It means the order not change status
		if(!isset($newSaleOrder["status"])) return; 

        $oldStatus = !is_null($oldSaleOrder) ? $oldSaleOrder["status"] : 'hold';//default status hold
        $newStatus = $newSaleOrder["status"];
        $orderedQty = isset($newSaleOrder["ordered_qty"]) ? $newSaleOrder["ordered_qty"] : $oldSaleOrder["ordered_qty"];//old sale order will has all records
        $orderAmount = isset($newSaleOrder["order_total"]) ? $newSaleOrder["order_total"] : $oldSaleOrder["order_total"];//old sale order will has all records

        $orderSubTotal = isset($newSaleOrder["sub_total"]) ? $newSaleOrder["sub_total"] : $oldSaleOrder["sub_total"];//old sale order will has all records
        $orderPax = isset($newSaleOrder["people"]) ? $newSaleOrder["people"] : $oldSaleOrder["people"];//old sale order will has all records
        $orderDiscount = isset($newSaleOrder["discount_total"]) ? $newSaleOrder["discount_total"] : $oldSaleOrder["discount_total"];//old sale order will has all records
        $orderVatExempt = isset($newSaleOrder["vat_exempt_total"]) ? $newSaleOrder["vat_exempt_total"] : $oldSaleOrder["vat_exempt_total"];//old sale order will has all records
        $orderVattaxable = isset($newSaleOrder["vat_taxable_total"]) ? $newSaleOrder["vat_taxable_total"] : $oldSaleOrder["vat_taxable_total"];//old sale order will has all records
        $orderTax = isset($newSaleOrder["tax_total"]) ? $newSaleOrder["tax_total"] : $oldSaleOrder["tax_total"];//old sale order will has all records

        
        $originalOrderDate = isset($newSaleOrder["order_date"]) ? $newSaleOrder["order_date"] : (isset($oldSaleOrder["order_date"]) ? $oldSaleOrder["order_date"] : null);//old sale order will has all records


        $orderDate = is_null($originalOrderDate) ? Carbon::now()->format('Y-m-d') : Carbon::createFromFormat(GlobalStaticValue::$FORMAT_DATETIME, $originalOrderDate)->format('Y-m-d');
        $orderTime = is_null($originalOrderDate) ? Carbon::now()->format('H:00') : Carbon::createFromFormat(GlobalStaticValue::$FORMAT_DATETIME, $originalOrderDate)->format('H:00');

        $orderType = isset($newSaleOrder["so_type"]) ? $newSaleOrder["so_type"] : $oldSaleOrder["so_type"];//old sale order will has all records

        $reportSaleSummaryController = new ReportSaleSummaryController(); 
        $defaultReport = [
            "total_qty"         => 0, 
            "total_amount"      => 0,
            "total_sub_amount"  => 0,
            "total_discount"    => 0,
            "total_vat_exempt"  => 0,
			"total_pax"         => 0,
			"total_vat_taxable"	=> 0,
			"total_tax" 		=> 0
        ]; 
        //get existed report to calc
        $lstExistedReports = ReportSaleSummary::whereDate("sale_date", $orderDate)
                                                ->where("order_type", $orderType)
                                                ->where("sale_time", $orderTime)
                                                ->get()->toArray();
        $existedReport = !empty($lstExistedReports) ? $lstExistedReports[0] : $defaultReport;

        //if order change status to completed => add amount and qty to that date
        if($oldStatus != $newStatus && $newStatus == 'completed'){
            $newReport = [
                "id" 				=> isset($existedReport["id"]) ? $existedReport["id"] : null,
                "total_qty" 		=> $orderedQty + $existedReport["total_qty"],
                "total_amount" 		=> $orderAmount + $existedReport["total_amount"],
				"total_sub_amount"  => $orderSubTotal + $existedReport["total_sub_amount"],
				"total_discount"    => $orderDiscount + $existedReport["total_discount"],
				"total_vat_exempt"  => $orderVatExempt + $existedReport["total_vat_exempt"],
				"total_pax"         => $orderPax + $existedReport["total_pax"],
				"total_vat_taxable"	=> $orderVattaxable + $existedReport["total_vat_taxable"],
				"total_tax" 		=> $orderTax + $existedReport["total_tax"]
            ];

            //for update
            if(isset($existedReport["id"])){
                $reportSaleSummaryController->updateLocal([$newReport]);    
            }else{
                $newReport["sale_date"] = $orderDate;
                $newReport["order_type"] = $orderType;
                $newReport["sale_time"] = $orderTime;

                $reportSaleSummaryController->createLocal([$newReport]);
            }
            
			
            //do create report sale detail
            ReportSaleDetailHandler::calcSaleDetailOnCompletedOrder($newSaleOrder);
        }

        //if order change status from completed, we need to minus amount and qty from that date
        if($oldStatus == 'completed' && $oldStatus != $newStatus && $newStatus != 'completed'){
            $totalQty = ($existedReport["total_qty"] > 0 ? ($existedReport["total_qty"] - $orderedQty) : $orderedQty);
            $totalQty = $totalQty < 0 ? 0 : $totalQty;

            $totalAmt = ($existedReport["total_amount"] > 0 ? ($existedReport["total_amount"] - $orderAmount) : $orderAmount);
			$totalAmt = $totalAmt < 0 ? 0 : $totalAmt;
			
			$totalSubAmt = ($existedReport["total_sub_amount"] > 0 ? ($existedReport["total_sub_amount"] - $orderSubTotal) : $orderSubTotal);
			$totalSubAmt = $totalSubAmt < 0 ? 0 : $totalSubAmt;

			$totalDsicount = ($existedReport["total_discount"] > 0 ? ($existedReport["total_discount"] - $orderDiscount) : $orderDiscount);
			$totalDsicount = $totalDsicount < 0 ? 0 : $totalDsicount;

			$totalVatExempt = ($existedReport["total_vat_exempt"] > 0 ? ($existedReport["total_vat_exempt"] - $orderVatExempt) : $orderVatExempt);
			$totalVatExempt = $totalVatExempt < 0 ? 0 : $totalVatExempt;

			$totalPax = ($existedReport["total_pax"] > 0 ? ($existedReport["total_pax"] - $orderPax) : $orderPax);
			$totalPax = $totalPax < 0 ? 0 : $totalPax;

			$totalVatTaxable = ($existedReport["total_vat_taxable"] > 0 ? ($existedReport["total_vat_taxable"] - $orderVattaxable) : $orderVattaxable);
			$totalVatTaxable = $totalVatTaxable < 0 ? 0 : $totalVatTaxable;

			$totalTax = ($existedReport["total_tax"] > 0 ? ($existedReport["total_tax"] - $orderTax) : $orderTax);
			$totalTax = $totalTax < 0 ? 0 : $totalTax; 

            $newReport = [
                "id" => isset($existedReport["id"]) ? $existedReport["id"] : null,
                "total_qty" => $totalQty,
				"total_amount" => $totalAmt,
				"total_sub_amount"  => $totalSubAmt,
				"total_discount"    => $totalDsicount,
				"total_vat_exempt"  => $totalVatExempt,
				"total_pax"         => $totalPax,
				"total_vat_taxable"	=> $totalVatTaxable,
				"total_tax" 		=> $totalTax
            ];
            //for update
            if(isset($existedReport["id"])){
                $reportSaleSummaryController->updateLocal([$newReport]);  
            }else{
                $newReport["sale_date"] = $orderDate;
                $newReport["order_type"] = $orderType;
                $newReport["sale_time"] = $orderTime;
                $reportSaleSummaryController->createLocal([$newReport]);
            }

            //do create report sale detail
            ReportSaleDetailHandler::calcSaleDetailUpdateFromCompletedOrder($newSaleOrder);
        }
    }

    /**
     * Method to remove amount and qty from report
     * @param $saleOrder        Sale Order record that has deleted
     */
    public static function calcSaleSummaryReportOnDel($saleOrder){
        if(!isset($saleOrder["order_date"])) return;

        $orderType = $saleOrder["so_type"];

        $orderDate = $saleOrder["order_date"];
        $orderDate = Carbon::createFromFormat(GlobalStaticValue::$FORMAT_DATETIME, $orderDate)->format('Y-m-d');
        $orderTime = Carbon::createFromFormat(GlobalStaticValue::$FORMAT_DATETIME, $orderDate)->format('H:00');

        //get existed report to calc
        $lstExistedReports = ReportSaleSummary::whereDate("sale_date", $orderDate)
                                                ->where("order_type", $orderType)
                                                ->where("sale_time", $orderTime)
                                                ->get()->toArray();

        if(empty($lstExistedReports)) return;

        $orderedQty = isset($saleOrder["ordered_qty"]) ? $saleOrder["ordered_qty"] : 0;
		$orderAmount = isset($saleOrder["order_total"]) ? $saleOrder["order_total"] : 0;
		$orderSubTotal = isset($saleOrder["sub_total"]) ? $saleOrder["sub_total"] : 0;
        $orderPax = isset($saleOrder["people"]) ? $saleOrder["people"] : 0;
        $orderDiscount = isset($saleOrder["discount_total"]) ? $saleOrder["discount_total"] : 0;
        $orderVatExempt = isset($saleOrder["vat_exempt_total"]) ? $saleOrder["vat_exempt_total"] : 0;
        $orderVattaxable = isset($saleOrder["vat_taxable_total"]) ? $saleOrder["vat_taxable_total"] : 0;
		$orderTax = isset($saleOrder["tax_total"]) ? $saleOrder["tax_total"] : 0;
		
        
        $existedReport = $lstExistedReports[0];

        $totalQty = ($existedReport["total_qty"] > 0 ? ($existedReport["total_qty"] - $orderedQty) : $orderedQty);
        $totalQty = $totalQty < 0 ? 0 : $totalQty;

        $totalAmt = ($existedReport["total_amount"] > 0 ? ($existedReport["total_amount"] - $orderAmount) : $orderAmount);
		$totalAmt = $totalAmt < 0 ? 0 : $totalAmt;
		
		$totalSubAmt = ($existedReport["total_sub_amount"] > 0 ? ($existedReport["total_sub_amount"] - $orderSubTotal) : $orderSubTotal);
		$totalSubAmt = $totalSubAmt < 0 ? 0 : $totalSubAmt;

		$totalDsicount = ($existedReport["total_discount"] > 0 ? ($existedReport["total_discount"] - $orderDiscount) : $orderDiscount);
		$totalDsicount = $totalDsicount < 0 ? 0 : $totalDsicount;

		$totalVatExempt = ($existedReport["total_vat_exempt"] > 0 ? ($existedReport["total_vat_exempt"] - $orderVatExempt) : $orderVatExempt);
		$totalVatExempt = $totalVatExempt < 0 ? 0 : $totalVatExempt;

		$totalPax = ($existedReport["total_pax"] > 0 ? ($existedReport["total_pax"] - $orderPax) : $orderPax);
		$totalPax = $totalPax < 0 ? 0 : $totalPax;

		$totalVatTaxable = ($existedReport["total_vat_taxable"] > 0 ? ($existedReport["total_vat_taxable"] - $orderVattaxable) : $orderVattaxable);
		$totalVatTaxable = $totalVatTaxable < 0 ? 0 : $totalVatTaxable;

		$totalTax = ($existedReport["total_tax"] > 0 ? ($existedReport["total_tax"] - $orderTax) : $orderTax);
		$totalTax = $totalTax < 0 ? 0 : $totalTax; 

        $newReport = [
            "id" => $existedReport["id"],
            "total_qty" => $totalQty,
			"total_amount" => $totalAmt,
			"total_sub_amount"  => $totalSubAmt,
			"total_discount"    => $totalDsicount,
			"total_vat_exempt"  => $totalVatExempt,
			"total_pax"         => $totalPax,
			"total_vat_taxable"	=> $totalVatTaxable,
			"total_tax" 		=> $totalTax
        ];

        //for update 
        $reportSaleSummaryController = new ReportSaleSummaryController(); 
        $reportSaleSummaryController->updateLocal([$newReport]);

        //do create report sale detail
        ReportSaleDetailHandler::calcSaleDetailUpdateFromCompletedOrder($saleOrder);
    }
}
