<?php

namespace App\Services;

use App\Http\Controllers\API\PurchaseReceiptDetailController;
use Illuminate\Database\Eloquent\Model;
use App\Model\PurchaseReceiptDetail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Exceptions\CustomException;

class PurchaseReceivingDetailHandler
{
    
    public static function updateReceiptDetail($oldRecordReceiptDetail)
    {
        $pr_detailController = new PurchaseReceiptDetailController();

        if (isset($oldRecordReceiptDetail) && !empty($oldRecordReceiptDetail)) {

            $lst_pr_detail = [];
            $lst_pr_detail_old = [];
            $open_qty = 0;
            $totalOpen_qty = 0;

            
            foreach ($oldRecordReceiptDetail as $key => $valueReceiptDetail) {

                if (isset($valueReceiptDetail["prev_receive_qty"]) && !empty($valueReceiptDetail["prev_receive_qty"])) {
                   
                    if (isset($open_qty) && !empty($open_qty)) {
                        $totalOpen_qty = $valueReceiptDetail["order_details"]["qty"] - $open_qty;

                        $objectRD["id"] = $valueReceiptDetail["id"];
                        $objectRD["prev_receive_qty"] = $valueReceiptDetail["receive_qty"];
                        $objectRD["prev_open_qty"] = $totalOpen_qty;
                        $lst_pr_detail_old []= $objectRD;
                    }
                    
                }else{
                    $objectRD["id"] = $valueReceiptDetail["id"];
                    $objectRD["prev_receive_qty"] = 0;
                    $objectRD["prev_open_qty"] = $valueReceiptDetail["prev_open_qty"];
                    $lst_pr_detail []= $objectRD;

                    
                }
                $open_qty = $valueReceiptDetail["receive_qty"];
            }
            throw new CustomException("listRD =>", $lst_pr_detail_old);

            $pr_detailController->updateLocal([$lst_pr_detail]);
        }

    }
    
}