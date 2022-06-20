<?php

namespace App\Services;
use App\Model\ShipmentDetail;
use App\Model\SaleOrderDetail;
use App\Http\Controllers\API\SaleOrderDetailController;
class SalesOrderDetailHandler
{
    /**
     * Method to setup default field for sale order detail
     * @param $lstNewSODetails      list new sale order detail record
     */
    public static function setDefaultField(&$lstNewSODetails){

        foreach ($lstNewSODetails as $index => &$newSODetail) {
            //to make sure qty always 1
            $newSODetail["quantity"] = empty($newSODetail["quantity"]) || $newSODetail["quantity"] <= 0 ? 1 : $newSODetail["quantity"];
        }
    }

    /** 
     * Update shipped quantity after CREATE shipment details 
     * @param $saleDetailId list of shipment details 
     */
    public static function updateSaleDetailShippedQuantity($saleDetailId) {

        $controller = new SaleOrderDetailController();

        $lstSaleDetialRecords = SaleOrderDetail::where("id", $saleDetailId)->get()->toArray();
        $saleDetailRecord = $lstSaleDetialRecords[0];

        if (isset($saleDetailId) && !empty($saleDetailId)) {

            $lstShipDetail = ShipmentDetail::where("sale_order_details_id", $saleDetailId)->get()->toArray();
            $totalShip = 0;

            if (isset($lstShipDetail) && !empty($lstShipDetail)) {

                foreach ($lstShipDetail as $index => $shipDetail) {
                    
                    if (empty($shipDetail["is_direct_create"]) || $shipDetail["is_direct_create"] == "0") {
                        
                        if (isset($shipDetail["ship_qty"]) && !empty($shipDetail["ship_qty"])) {
                            $totalShip += $shipDetail["ship_qty"];
                        }
                    }
                }

                $saleDetailRecord = [
                    "id" => $saleDetailId,
                    "shipped_qty" => $totalShip,
                    "prev_shipped_qty" => $totalShip,
                    "prev_open_qty" => $saleDetailRecord["quantity"] - $totalShip
                ];
                $controller->updateLocal([$saleDetailRecord]);
            }
        }
    }

    /** 
     * Function update shipped quantity of sale detail after DELETE shipment detail
     * @param $lstShipmentDetail 
     * */
    public static function reCalcSaleDetailShippedQuantity($lstShipmentDetail) {

        $controller = new SaleOrderDetailController();

        if (isset($lstShipmentDetail) && !empty($lstShipmentDetail)) {

            foreach ($lstShipmentDetail as $index => $shipmentDetail) {

                if (isset($shipmentDetail["sale_order_details_id"]) && !empty($shipmentDetail["sale_order_details_id"])) {

                    $lstOrderDetails = SaleOrderDetail::where("id", $shipmentDetail["sale_order_details_id"])->get()->toArray();
                    
                    if (isset($lstOrderDetails) && !empty($lstOrderDetails)) {
                        $saleDetail = $lstOrderDetails[0];

                        if (isset($saleDetail["shipped_qty"]) && !empty($saleDetail["shipped_qty"])) {
                            $saleDetail["shipped_qty"] -= $shipmentDetail["ship_qty"];

                            $saleDetailRecord = [
                                "id" => $saleDetail["id"],
                                "shipped_qty" => $saleDetail["shipped_qty"]
                            ];
                            $controller->updateLocal([$saleDetailRecord]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Method to get active order detail
     * @param $orderId      Sale order Id (optional)
     * @return list active order detail
     * @created 20-02-2021
     * @author Sopha PUM
     */
    public static function getActiveDetails($orderId = null){
        $query = SaleOrderDetail::where("status", '!=', 'deleted');

        if(!is_null($orderId)){
            $query->where("sales_order_id", $orderId);
        }

        return $query->get()->toArray();
    }

}
