<?php

namespace App\Services;

use App\Http\Controllers\API\IssueProductController;
use App\Model\Shipment;
use Carbon\Carbon;

class IssueProductHandler
{

    /**
     * Method create Issue Product record after released Shipment
     * @param $shipmentId       String shipment id released
     * @author Sopha Pum | 11-06-2021
     */
    public static function createIssueProdAfterReleasedShipment($shipmentId){
        $lstShipments = Shipment::where("id", $shipmentId)
                                    ->with("details")
                                    ->get()
                                    ->toArray();

        $shipment = $lstShipments[0];
        $lstShipDetails = $shipment["details"];

        $lstIssueDetails = [];
        foreach ($lstShipDetails as $key => $shipDetail) {
            $newIssueDetail = [
                "product_id" => $shipDetail["products_id"], 
                "trans_uom_id" => $shipDetail["uom_id"], 
                "base_uom_id" => $shipDetail["uom_id"], 
                "qty" => $shipDetail["ship_qty"]
            ];
            $lstIssueDetails[] = $newIssueDetail;
        }

        $newIssueProduct = [
            "issue_date" => new Carbon(), 
            "status" => "open",
            "source" => "shipment",
            "details" => $lstIssueDetails
        ];

        $issueController = new IssueProductController();
        $lstCreatedIssueProds = $issueController->createLocal([$newIssueProduct]);

        //update it as completed back to fire trigger
        $updateIssueProd = [
            "id" => $lstCreatedIssueProds[0]["id"],
            "status" => "completed"
        ];
        $issueController->updateLocal([$updateIssueProd]);
    }
}
