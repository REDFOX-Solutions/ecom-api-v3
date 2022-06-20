<?php

namespace App\Services;

use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\InvoiceDetailController;
use App\Http\Controllers\API\InvoiceReceiptController;
use App\Http\Controllers\API\ReceiptController;
use App\Http\Controllers\API\SaleOrderController;
use App\Http\Controllers\API\SaleOrderDetailController;
use App\Http\Controllers\API\ShipmentController;
use App\Http\Controllers\API\ShipmentDetailController;
use App\Http\Controllers\ResponseHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class POSHandler extends Model
{
    /**
     * Method for POS Sale make payment
     * @param $saleOrder        Sale Order record
     * @param $lstReceipts      List Payments
     * @return ResponseHandler
     * @created 20-02-2021
     * @author Sopha Pum
     */
    public static function makePayment($saleOrder, $lstReceipts){
        try{   
            if(empty($saleOrder["id"])) return ResponseHandler::clientError("Invalid Sale Record!");

            //We dont allow general customer credit the amount, so we will check the sale total amount with payment
            if((empty($lstReceipts) || count($lstReceipts) <= 0) && !isset($saleOrder["customer_id"])) return ResponseHandler::clientError("General customer cannot credit!");
             
            $saleOrder["status"] = 'confirmed';
            $saleOrder["receipts"] = $lstReceipts;
            $orderCtrler = new SaleOrderController();
            $orderCtrler->updateLocal([$saleOrder]);  
            
            return ResponseHandler::showSuccess([]);
        }catch(\Exception $ex){
            return ResponseHandler::customException($ex); 
        }
    }
 
}
