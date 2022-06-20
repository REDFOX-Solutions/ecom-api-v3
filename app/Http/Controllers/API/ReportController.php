<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RestResource;
use App\Model\ReportSaleDetail;
use App\Model\ReportSaleSummary;
use App\Model\SaleOrder;
use App\Model\SaleOrderDetail;

class ReportController extends RestAPI
{
    public function getTableSetting()
    {
        return [];
    }

    public function getQuery()
    {
        return null;
    }

    public function getModel()
    {
        return 'App\Model\Post';
    }

    /**
     * Report Sales Order Detail
     * @filter 
     *      start_date | requried       string format yyyy-mm-dd
     *      end_date                    if there are no end date, we will use start date as end date
     * @return list Sales Order detail
     */
    public function reportSaleDetail(Request $request)
    {
        try {
            $lstFilter = $request->all();

            // return $lstFilter['start_date'];

            if (!isset($lstFilter['start_date'])) {
                return $this->clientError("Start date required!");
            }

            if (!isset($lstFilter["end_date"])) {
                $lstFilter["end_date"] = $lstFilter["start_date"];
            }

            $sDate = $lstFilter["start_date"];
            $eDate = $lstFilter["end_date"];

            //get sale order detail where sale order status = completed
            $queryOrder = ReportSaleDetail::whereDate("sale_date", ">=", $sDate)
                                        ->whereDate("sale_date", "<=", $eDate);

            $lstResults = $queryOrder->get();

            return RestResource::collection($lstResults);
        } catch (\Exception $ex) {
            return $this->respondError($ex);
        }
    }

    /**
     * Report sale summary by date
     * We will has 2 options: 
     *  - get existed report to show them
     *  - recalc report store in DB and show them
     * @param $request      object filter from client request
     *  Object
     *      - start_date | required        start date for filter
     *      - end_date                      end date for filter, default equals start date
     */
    public function reportSaleSummary(Request $request)
    {
        try {
            $lstFilter = $request->all();

            // return $lstFilter['start_date'];

            if (!isset($lstFilter['start_date'])) {
                return $this->clientError("Start date required!");
            }

            if (!isset($lstFilter["end_date"])) {
                $lstFilter["end_date"] = $lstFilter["start_date"];
            }

            $sDate = $lstFilter["start_date"];
            $eDate = $lstFilter["end_date"];

            //sum amount and qty by date from sale order
            //group by date
            //date, total order qty, total order amount

            //get sale order  where sale order status = completed
            $queryOrder = ReportSaleSummary::whereDate("sale_date", ">=", $sDate)
                ->whereDate("sale_date", "<=", $eDate);

            $lstResults = $queryOrder->get();

            return RestResource::collection($lstResults);
        } catch (\Exception $ex) {
            return $this->respondError($ex);
        }
    }
}
