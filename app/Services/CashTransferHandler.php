<?php

namespace App\Services;

use Carbon\Carbon;
use App\Model\CashTransferDetail;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\API\CashTransferController;

use App\Http\Controllers\API\CashTransferDetailController;
use App\Model\CashTransfer;

class CashTransferHandler
{
    public static function setDefaultValue(&$lstCashTransfer)
    {
        foreach ($lstCashTransfer as $key => &$cashtransfer) {
            $transferDate = new Carbon();
            if (!isset($cashtransfer["transfer_date"]) || empty($cashtransfer["transfer_date"])) {
                $cashtransfer["transfer_date"] = $transferDate;
            }

            if (empty($cashtransfer["ref_num"]) || $cashtransfer["ref_num"] == null) {
                $cashtransfer["ref_num"] = DatabaseGW::generateReferenceCode('cash_transfer');
            }
        }
    }


    //run when delete cash transfer it's will delete cash transfer-detail
    public static function deleteCADetail($listCA)
    {
        $lstGetIds = [];
        foreach ($listCA as $indx => $cash) {

            $lstIds = CashTransferDetail::where('cash_transfer_id', $cash["id"])->get()->toArray();

            foreach ($lstIds as $idex => $cashDetail) {
                $lstGetIds[] = [
                    "id" => $cashDetail["id"]
                ];
                # code...
            }
            $controllerDetail = new CashTransferDetailController();
            $controllerDetail->delete($lstGetIds);
        }
    }
    // delete cash transfer detail when update transfer type !=cash entry
    public static function deleteCA($cashIs)
    { 
        $lstIds = CashTransferDetail::where('cash_transfer_id', $cashIs)
                                    ->where('detail_type', '=', 'receipt')
                                    ->get()
                                    ->keyBy("id")
                                    ->keys()
                                    ->all();
        if (!empty($lstIds)) {
            $con = new CashTransferDetailController();
            $con->delete($lstIds);
        }
    }
    public static function isVoid($recordsId)
    {


        $lstCTs = CashTransfer::where("id", $recordsId)->with("cashTransferDetails")->get()->toArray();
        $lstUpdate = [];
        foreach ($lstCTs as $key => $originCT) {
            $void = $originCT;
            unset($void["id"]);
            $void["status"] = "open";
            $void["is_void"] = 1;

            $form_cash = $void["from_cash_acc_id"];
            $to_cash = $void["to_cash_acc_id"];

            $void["to_cash_acc_id"] = $form_cash;
            $void["from_cash_acc_id"] = $to_cash;

            $lstVoidDetails = $void["cash_transfer_details"];
            if ($lstVoidDetails) {
                foreach ($lstVoidDetails as $key => &$voidDetail) {
                    unset($voidDetail["id"]);
                }
            }


            $void["cash_transfer_details"] = $lstVoidDetails;
            // unset($void["cash_transfer_details"]["id"]);

            if (isset($void["is_void"]) && $originCT["is_void"] != $void["is_void"]) {

                $lstUpdate = $void;
            }
            if ($lstUpdate) {
                $controller = new CashTransferController();
                $controller->createLocal([$lstUpdate]);
            }


            # code...
        }
    }
}
