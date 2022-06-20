<?php

namespace App\Services;

use App\Http\Controllers\API\ReceiptProductController;
use App\Model\PurchaseReceipt;
use Carbon\Carbon;

class ReceiptProductHandler
{
    /**
     * Method create Receipt Product record after released purchase receipt
     * @param $prId       String purchase receipt id 
     * @author Sopha Pum | 11-06-2021
     */
    public static function createReceiptProdAfterReleasedReceipt($prId){
        $today = new Carbon();
        $lstPurchaseReceipt = PurchaseReceipt::where("id", $prId)
                                    ->with("receiptDetails")
                                    ->get()
                                    ->toArray();

        $receipt = $lstPurchaseReceipt[0];
        $lstPrDetails = $receipt["receipt_details"];

        $lstDetails = [];
        foreach ($lstPrDetails as $key => $prDetail) {
            $newDetail = [
                "product_id" => $prDetail["product_id"], 
                "trans_uom_id" => $prDetail["uom_id"], 
                "base_uom_id" => $prDetail["uom_id"], 
                "qty" => $prDetail["receive_qty"]
            ];
            $lstDetails[] = $newDetail;
        }

        $newReceiptProduct = [
            "receipt_date" => $today->format(GlobalStaticValue::$FORMAT_DATETIME), 
            "status" => "open",
            "source" => "pruchase",
            "details" => $lstDetails
        ];

        $receiptController = new ReceiptProductController();
        $lstCreated = $receiptController->createLocal([$newReceiptProduct]);

        //update it as completed back to fire trigger
        $updateReceiptProd = [
            "id" => $lstCreated[0]["id"],
            "status" => "completed"
        ];
        $receiptController->updateLocal([$updateReceiptProd]);
    }
}
