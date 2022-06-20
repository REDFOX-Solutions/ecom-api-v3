<?php

namespace App\Services;

use App\Http\Controllers\API\MetadataConfigController;
use App\Http\Controllers\API\SaleOrderController; 
use App\Http\Controllers\API\ShipmentController;
use App\Http\Controllers\API\ShipmentDetailController;
use App\Model\SaleOrder;
use App\Model\SaleOrderDetail;
use App\Model\Settings;
use App\Model\MetaDataConfig;
use App\Model\Shipment;
use App\Model\ShipmentDetail;
use App\Services\InvoiceHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShipmentHandler
{
    protected static $CONST_REC_NUM = "shipment_num";
    protected static $RECP_PREFIX = "shipment_prefix";

    public static function addShipmentNumber(&$lstRecords){

        //get current shipment number from setting
        $lstShipNum = MetaDataConfig::where("name", self::$CONST_REC_NUM)->get()->toArray(); 
        $lstPrefixes = MetaDataConfig::where("name", self::$RECP_PREFIX)->get()->toArray();

        $settingCtrl = new MetadataConfigController();

        $startNum = !empty($lstShipNum) ? (int) $lstShipNum[0]["value"] : 0;
        $prefix = !empty($lstPrefixes) ? (int) $lstPrefixes[0]["value"] : 0;

        foreach ($lstRecords as $key => &$record) { 
            $startNum++;
            $record["ship_num"] = $prefix . $startNum;
            
            if (empty($record["ship_by_id"])) {
                $record["ship_by_id"] = Auth::user()->id;
            }
            // $record["ship_by_id"] = !empty($record["ship_by_id"]) ? $record["ship_by_id"] : Auth::user()->id;
        }
  
        //update setting back
        //if there are no existed setting for receipt number, we need to create it
        if(empty($lstShipNum)){
            $setting = [
                "name" => self::$CONST_REC_NUM,
                "value" => $startNum,
                "company_id" => Auth::user()->company_id
            ]; 
            $settingCtrl->createLocal([$setting]);
        }else{
            $lstShipNum[0]["value"] = $startNum;
            $settingCtrl->upsertLocal($lstShipNum);
        }
    }

    /**
     * Method to update Sales Order status to completed after shipment is completed
     * @param $newShipment  new shipment record that has updated completed
     */
    // public static function check2UpdateOrderStatus($oldShipment, $newShipment){

    //     //if shipment has changed to completed and all shipments are completed, 
    //     //we will update order to completed
    //     if(isset($newShipment) && !empty($newShipment) &&
    //         $oldShipment["status"] != $newShipment["status"] &&
    //         $newShipment["status"] == 'completed')
    //     {

    //         $saleOrderId = isset($newShipment["sales_order_id"]) ? $newShipment["sales_order_id"] : $oldShipment["sales_order_id"];

    //         $lstOrders = SaleOrder::where("id", $saleOrderId)->get()->toArray();
    //         $order = $lstOrders[0];

    //         //check number of shipped item in order. if it isn't the same ordered item, do nothing
    //         if($order["shipped_qty"] == $order["ordered_qty"]){

    //             //check if all shipment record has completed
    //             //get all shipment records related order
    //             $numUncompleted = Shipment::where("sales_order_id", $order["id"])
    //                                         ->where("status", "<>", "completed")
    //                                         ->get()
    //                                         ->count();

    //             //if all shipments are completed, we will update order to completed
    //             if($numUncompleted <= 0){
    //                 $updateOrder = [
    //                     "id" => $order["id"],
    //                     "status" => "completed",
    //                     "checkout_date" => Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME),
    //                     "completed_by_id" => Auth::user()->id
    //                 ];
    //                 $orderCtler = new SaleOrderController();
    //                 $orderCtler->updateLocal([$updateOrder]);
    //             }
    //         }
    //     }
    // }
 
    /**
     * Method to update shipment after invoice is completed
     * @param $lstInvIds        array string of invoice ids
     * @return void
     */
    public static function updateShipmentOnInvCompleted($lstInvIds){
        $lstShipment2Update = [];
        //get related shipment and update it to completed
        Shipment::whereIn('invoices_id', $lstInvIds)->get()->each(function($shipment, $key) use (&$lstShipment2Update){
            $newShipment = [
                "id" => $shipment["id"],
                "status" => "closed"
            ];
            $lstShipment2Update[] = $newShipment;
        }); 
        if(!empty($lstShipment2Update)){
            $shipmentCtrler = new ShipmentController();
            $shipmentCtrler->updateLocal($lstShipment2Update);
        }
    }

    /**
     * Method to check Sale Order status after shipment has updated to completed
     * This method will check related shipment with Sales order
     * @param $shipId       String shipment id
     * @return void
     * @author Sopha Pum
     */
    public static function reCheckSaleOrderStatusAfterShipmentCompleted($shipId){
        
        //get all related sales order with the updated shipment
        $lstShipmentDetails = ShipmentDetail::where("shipments_id", $shipId)
                                            ->get()
                                            ->toArray();

        $saleOrderIds = [];
        foreach ($lstShipmentDetails as $index => $shipmentDetail) {
            if(isset($shipmentDetail["sales_order_id"])){
                $saleOrderIds[] = $shipmentDetail["sales_order_id"];
            }
            
        }

        //get all related shipments with sale order to check if all shipment status equals completed
        //we will update sale order to completed too
        $mapSORelatedShipments = ShipmentDetail::whereIn("sales_order_id", $saleOrderIds)
                                                ->with("shipment")
                                                ->get()
                                                ->mapToGroups(function($item, $key){
                                                    return[$item["sales_order_id"] => $item];
                                                })
                                                ->all();

        $lstSO2UpdateStatus = [];
        foreach ($mapSORelatedShipments as $soId => $lstShipDetails) {
            
            $isCompleted = true;
            foreach ($lstShipDetails as $index => $shipDetail) {
                $shipment = $shipDetail["shipment"];

                if(isset($shipment["status"]) && $shipment["status"] != 'closed'){
                    $isCompleted = false;
                }
            }

            //if there are no completed shipment, we don't update sales order status to completed
            if($isCompleted == true){
                $newSO = ["id" => $soId, "status" => "closed"];
                $lstSO2UpdateStatus[] = $newSO;
            }
        }

        if(!empty($lstSO2UpdateStatus)){
            $soController = new SaleOrderController();
            $soController->updateLocal($lstSO2UpdateStatus);
        }
    }

    /**
     * Method to update sale order qty if shipment is open
     * @param $oldShipment      existed shipment record
     * @param $newShipment      updated shipment record
     * @return void
     */
    public static function doUpdateSaleOrderQTY($oldShipment, $newShipment){

        $oldStatus = $oldShipment == null ? 'hold' : (empty($oldShipment["status"]) ? 'hold':  $oldShipment["status"]);
        $newStatus = (empty($newShipment["status"]) ? 'hold':  $newShipment["status"]);
        
        if($oldStatus != $newStatus && $newStatus == 'open')
        {
            $saleOrderId = isset($newShipment["sales_order_id"]) ? $newShipment["sales_order_id"] : $oldShipment["sales_order_id"];

            $lstOrders = SaleOrder::where("id", $saleOrderId)->get()->toArray();
            $order = $lstOrders[0];

            $existedShippedQty = empty($order["shipped_qty"]) ? 0 : $order["shipped_qty"];
            $newShippedQty = empty($newShipment["total_qty"]) ? 0 : $newShipment["total_qty"];

            $newOrder = [
                "id" => $order["id"],
                "shipped_qty" => $existedShippedQty + $newShippedQty
            ];
 
            $orderController = new SaleOrderController();
            $orderController->updateLocal([$newOrder]);
        }
    }


    /** 
     * Method use to recalculate shipment quantity after shipment detail changed
     * @param $shipId String shipment id
     */
    public static function reCalcShipmentQty($shipId) {
        $controller = new ShipmentController();
        $totalQty = 0;

        $lstShipmentDetail = ShipmentDetail::where("shipments_id", $shipId)->get()->toArray();
        foreach ($lstShipmentDetail as $key => $value) {
            $qty = empty($value["ship_qty"]) ? 1 : $value["ship_qty"];
            $totalQty += $qty;
        }

        $lstShipments = Shipment::where("id", $shipId)->get()->toArray();
        $existedShipment = $lstShipments[0];

        /** Update shipment quantity */
        $shipment = [
            "id" => $shipId,
            "total_qty" => $totalQty,
            "status" => $existedShipment["status"]
        ];

        $controller->updateLocal([$shipment]);
    }
}
