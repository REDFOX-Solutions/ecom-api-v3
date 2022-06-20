<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Http\Controllers\API\ReportSaleByChannelController;
use App\Http\Controllers\API\ReportSaleByItemController;
use App\Http\Controllers\API\ReportSaleSummaryController;
use App\Model\Company;
use App\Model\ReportSaleByChannel;
use App\Model\ReportSaleByItem;
use App\Model\ReportSaleSummary;
use App\Model\SaleOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ReportSaleHandler extends Model
{
    public static function logSaleSummary($saleOrder){
        $lstCompanies = Company::where("id", Auth::user()->company_id)
                                ->get()
                                ->toArray();
        $companyTimeZone = isset($lstCompanies[0]["default_timezone"]) ? $lstCompanies[0]["default_timezone"] : "UTC";

        //select newSaleOrder back from DB
        $lstNewSaleOrder = SaleOrder::where("id", $saleOrder["id"])
                                    ->with(["activeOrderDetails", "pricebook"])
                                    ->get()
                                    ->toArray();
        $newSaleOrder = $lstNewSaleOrder[0];
 
        $orderedQty = isset($newSaleOrder["ordered_qty"]) ? $newSaleOrder["ordered_qty"] : 0;
        $orderAmount = isset($newSaleOrder["order_total"]) ? $newSaleOrder["order_total"] : 0;
        $orderSubTotal = isset($newSaleOrder["sub_total"]) ? $newSaleOrder["sub_total"] : 0;
        $orderPax = isset($newSaleOrder["people"]) ? $newSaleOrder["people"] : 1;
        $orderDiscount = isset($newSaleOrder["discount_total"]) ? $newSaleOrder["discount_total"] : 0;
        $orderVatExempt = isset($newSaleOrder["vat_exempt_total"]) ? $newSaleOrder["vat_exempt_total"] : 0;
        $orderVattaxable = isset($newSaleOrder["vat_taxable_total"]) ? $newSaleOrder["vat_taxable_total"] : 0;
        $orderTax = isset($newSaleOrder["tax_total"]) ? $newSaleOrder["tax_total"] : 0;
        $originalOrderDate = isset($newSaleOrder["order_date"]) ? $newSaleOrder["order_date"] : null;
        $orderTotalCost = isset($newSaleOrder["total_cost"]) ? $newSaleOrder["total_cost"] : 0;


        $orderDate = is_null($originalOrderDate) ? Carbon::now($companyTimeZone)->format('Y-m-d') : 
                                                    Carbon::createFromFormat(GlobalStaticValue::$FORMAT_DATETIME, $originalOrderDate)->setTimezone($companyTimeZone)->format('Y-m-d');
   

        $reportSaleSummaryController = new ReportSaleSummaryController(); 
        $defaultReport = [
            "sale_date"         => $orderDate,
            "total_qty"         => 0, 
            "total_amount"      => 0,
            "total_sub_amount"  => 0,
            "total_discount"    => 0,
            "total_vat_exempt"  => 0,
            "total_pax"         => 0,
            "total_vat_taxable"	=> 0,
            "total_tax" 		=> 0,
            "total_cost"        => 0
        ]; 
        //get existed report to calc
        $lstExistedReports = ReportSaleSummary::whereDate("sale_date", $orderDate)
                                                ->get()
                                                ->toArray();
        $existedReport = !empty($lstExistedReports) ? $lstExistedReports[0] : $defaultReport;

        //update report sale summary
        $existedReport["total_qty"] += $orderedQty;
        $existedReport["total_amount"] += $orderAmount;
        $existedReport["total_sub_amount"] += $orderSubTotal;
        $existedReport["total_discount"] += $orderDiscount;
        $existedReport["total_vat_exempt"] += $orderVatExempt;
        $existedReport["total_vat_taxable"] += $orderVattaxable;
        $existedReport["total_tax"] += $orderTax;
        $existedReport["total_pax"] += $orderPax; 
        $existedReport["total_cost"] += $orderTotalCost; 

        $lstUpdatedReports = (isset($existedReport["id"]) && !is_null($existedReport["id"])) ? 
                                    $reportSaleSummaryController->updateLocal([$existedReport]) : 
                                    $reportSaleSummaryController->createLocal([$existedReport]);

        self::logSaleByChannel($newSaleOrder, $lstUpdatedReports[0]["id"]);

        if(isset($newSaleOrder["active_order_details"]) && !empty($newSaleOrder["active_order_details"])){ 
            self::logSaleByItem($newSaleOrder, $lstUpdatedReports[0]["id"]);   
        }
 
    }

    /**
     * Method log sale order by Product, Pricebook and Sale Hour
     * @param $saleOrder        Completed Sale order record
     * @param $reportSummaryId  String report summary
     * @return void
     * @author Sopha Pum | 21-07-2021
     */
    public static function logSaleByItem($saleOrder, $reportSummaryId){

        if(isset($saleOrder["active_order_details"]) && !empty($saleOrder["active_order_details"])){ 

            $lstCompanies = Company::where("id", Auth::user()->company_id)
                                ->get()
                                ->toArray();
            $companyTimeZone = isset($lstCompanies[0]["default_timezone"]) ? $lstCompanies[0]["default_timezone"] : "UTC";


            $pricebook = $saleOrder["pricebook"];
            $lstDetails = $saleOrder["active_order_details"];

            $originalOrderDate = isset($saleOrder["order_date"]) ? $saleOrder["order_date"] : null;
            $saleHour = is_null($originalOrderDate) ? Carbon::now($companyTimeZone)->format('H:00') : 
                                                        Carbon::createFromFormat(GlobalStaticValue::$FORMAT_DATETIME, $originalOrderDate)->setTimezone($companyTimeZone)->format('H:00');
                                                         
            $mapRptSaleItems = ReportSaleByItem::where("sale_summary_id", $reportSummaryId)
                                            ->get()
                                            ->keyBy(function($item){
                                                return $item["product_id"] . '_' . $item["pricebook_id"] . '_' . $item["sale_hour"];
                                            })
                                            ->all();
                                            
            foreach ($lstDetails as $key => $orderDetail) {
                $keyProdPbHour = $orderDetail["products_id"] . "_" . $pricebook["id"] . "_" . $saleHour;
                $product = $orderDetail["product"];
                $category = $product["category"]; 

                DataConvertionClass::findLangs2ChangeIndex($product);
                DataConvertionClass::findLangs2ChangeIndex($category);

                $defaultRpt = [
                    "product_id" => $orderDetail["products_id"], 
                    "product_name" => isset($product["langs"]) && isset($product["langs"]["en"]) && isset($product["langs"]["en"]["name"]) ? $product["langs"]["en"]["name"]: var_dump($product["langs"]), 
                    "category_id" => $product["default_category_id"], 
                    "category_name" => isset($category["langs"]) && isset($category["langs"]["en"]) && isset($category["langs"]["en"]["name"]) ? $category["langs"]["en"]["name"]: null,
                    "pricebook_id" => $pricebook["id"],
                    "pricebook_name" => $pricebook["name"], 
                    "sale_summary_id" => $reportSummaryId,
                    "sale_hour" => $saleHour,
                    "uom_id" => $orderDetail["uom_id"], 
                    "total_qty" => 0, 
                    "total_amount" => 0, 
                    "total_discount" => 0, 
                    "unit_price" => 0, 
                    "sub_amt" => 0,
                    "total_cost" => 0
                ];
                
                $cost =  isset($orderDetail["unit_cost"])? $orderDetail["unit_cost"] : 0;
                $qty = isset($orderDetail["quantity"]) ? $orderDetail["quantity"] : 1;
                $unitPrice = isset($orderDetail["unit_price"]) ? $orderDetail["unit_price"] : 0;
                $subAmt = isset($orderDetail["amount"]) ? $orderDetail["amount"] : 0;
                $disAmt = isset($orderDetail["discount_amount"]) ? $orderDetail["discount_amount"] : 0;
                $totalAmt = isset($orderDetail["total_amount"]) ? $orderDetail["total_amount"] : 0;
 
                $existedRptItem = isset($mapRptSaleItems[$keyProdPbHour]) ? $mapRptSaleItems[$keyProdPbHour]: $defaultRpt;
                $existedRptItem["total_qty"] +=  $qty;
                $existedRptItem["unit_price"] += $unitPrice;
                $existedRptItem["sub_amt"] += $subAmt;
                $existedRptItem["total_discount"] += $disAmt;
                $existedRptItem["total_amount"] += $totalAmt;
                $existedRptItem["total_cost"] += $cost;
                $mapRptSaleItems[$keyProdPbHour] = $existedRptItem;
            }

            //do upsert report detail
            $lstCreateRptSaleItems = [];
            $lstUpdateRptSaleItems = []; 
            
            foreach ($mapRptSaleItems as $keyProdPbHour2 => $rptSaleItem) {

                if(gettype($rptSaleItem) == 'object'){
                    $rptSaleItem = collect($rptSaleItem)->toArray();
                }
                if(isset($rptSaleItem["id"]) && !is_null($rptSaleItem["id"])){
                    $lstUpdateRptSaleItems[] = $rptSaleItem; 
                }else{
                    $lstCreateRptSaleItems[] = $rptSaleItem;  
                }
            }

            // throw new CustomException("number of " . $strTypeOf . '/' . $strTypeOfC, 404);

            $reportController = new ReportSaleByItemController();
            if(isset($lstUpdateRptSaleItems) && !empty($lstUpdateRptSaleItems)){
                $reportController->updateLocal($lstUpdateRptSaleItems);
            }

            if(isset($lstCreateRptSaleItems) && !empty($lstCreateRptSaleItems)){
                $reportController->createLocal($lstCreateRptSaleItems);
            }
        }
    }

    /**
     * Method log sale order by Channel and Sale Hour
     * @param $saleOrder        Completed Sale order record
     * @param $reportSummaryId  String report summary
     * @return void
     * @author Sopha Pum | 21-07-2021
     */
    public static function logSaleByChannel($saleOrder, $reportSummaryId){ 

        $lstCompanies = Company::where("id", Auth::user()->company_id)
                                ->get()
                                ->toArray();
        $companyTimeZone = isset($lstCompanies[0]["default_timezone"]) ? $lstCompanies[0]["default_timezone"] : "UTC";


        $channel = $saleOrder["channel"];

        $originalOrderDate = isset($saleOrder["order_date"]) ? $saleOrder["order_date"] : null;
        $saleHour = is_null($originalOrderDate) ? Carbon::now($companyTimeZone)->format('H:00') : 
                                                    Carbon::createFromFormat(GlobalStaticValue::$FORMAT_DATETIME, $originalOrderDate)->setTimezone($companyTimeZone)->format('H:00');
       
        $lstRptSaleChannels = ReportSaleByChannel::where("sale_summary_id", $reportSummaryId)
                                        ->where("channel", $channel)
                                        ->where("sale_hour", $saleHour)
                                        ->get()
                                        ->toArray();
        
        
        $orderedQty = isset($saleOrder["ordered_qty"]) ? $saleOrder["ordered_qty"] : 0;
        $orderAmount = isset($saleOrder["order_total"]) ? $saleOrder["order_total"] : 0;
        $orderSubTotal = isset($saleOrder["sub_total"]) ? $saleOrder["sub_total"] : 0;
        $orderPax = isset($saleOrder["people"]) ? $saleOrder["people"] : 1;
        $orderDiscount = isset($saleOrder["discount_total"]) ? $saleOrder["discount_total"] : 0;
        $orderTax = isset($saleOrder["tax_total"]) ? $saleOrder["tax_total"] : 0;
        $orderTotalCost = isset($saleOrder["total_cost"]) ? $saleOrder["total_cost"] : 0;

        $defaultRpt = [
            "sale_summary_id" => $reportSummaryId,
            "sale_hour" => $saleHour, 
            "channel" => $channel,
            "total_pax" => 0,
            "total_qty" => 0, 
            "sub_amt" => 0,
            "discount_amt" => 0,
            "vat_amt" => 0,
            "total_amt" => 0,
            "total_cost" => 0
        ];

        $existedSaleByChannel = !empty($lstRptSaleChannels) ? $lstRptSaleChannels[0] : $defaultRpt;
        $existedSaleByChannel["total_pax"] += $orderPax;
        $existedSaleByChannel["total_qty"] += $orderedQty;
        $existedSaleByChannel["sub_amt"] += $orderSubTotal;
        $existedSaleByChannel["discount_amt"] += $orderDiscount; 
        $existedSaleByChannel["vat_amt"] += $orderTax;
        $existedSaleByChannel["total_amt"] += $orderAmount;
        $existedSaleByChannel["total_cost"] += $orderTotalCost; 

        $reportController = new ReportSaleByChannelController();
        $lstUpserted = isset($existedSaleByChannel["id"]) && !is_null($existedSaleByChannel["id"]) ?
                            $reportController->updateLocal([$existedSaleByChannel]) :
                            $reportController->createLocal([$existedSaleByChannel]);


    }
}