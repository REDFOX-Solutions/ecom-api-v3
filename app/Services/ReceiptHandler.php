<?php

namespace App\Services; 

use App\Http\Controllers\API\MetadataConfigController;
use App\Http\Controllers\API\ReceiptController; 
use App\Http\Controllers\ResponseHandler; 
use App\Model\MetaDataConfig; 
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReceiptHandler
{
    protected static $CONST_REC_NUM = "receipt_num";
    protected static $RECP_PREFIX = "receipt_prefix";

    public static function setDefaultField(&$lstReceipts){
        $now = Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME); 

        //get default customer to update sales order customer if it didn't specific
        // $defaultCustomer = PersonAccountHandler::getDefaultCustomer();

        //to generate automatic order number
        self::addReceiptNumber($lstReceipts);

        foreach ($lstReceipts as $index => &$newReceipt) {
            $newReceipt["status"] = (!isset($newReceipt["status"]) || empty($newReceipt["status"])) ? "hold" : $newReceipt["status"];
            $newReceipt["receipt_date"] = (!isset($newReceipt["receipt_date"]) || empty($newReceipt["receipt_date"])) ? $now : $newReceipt["receipt_date"]; 
            $newReceipt["payment_method"] = (!isset($newReceipt["payment_method"]) || empty($newReceipt["payment_method"])) ? "cash" : $newReceipt["payment_method"];
        }
    }

    public static function addReceiptNumber(&$lstReceipts){

        //get current receipt number from setting
        $lstReceiptNum = MetaDataConfig::where("name", self::$CONST_REC_NUM)->get()->toArray(); 
        $lstReceiptNPrefix = MetaDataConfig::where("name", self::$RECP_PREFIX)->get()->toArray();

        $settingCtrl = new MetadataConfigController();

        $startNum = !empty($lstReceiptNum) ? (int) $lstReceiptNum[0]["value"] : 0;
        $prefix = !empty($lstReceiptNPrefix) ? (int) $lstReceiptNPrefix[0]["value"] : 0;

        foreach ($lstReceipts as $key => &$receipt) { 
            $startNum++;
            $receipt["receipt_num"] = $prefix . $startNum; 
        }
 
        //update setting back
        //if there are no existed setting for receipt number, we need to create it
        if(empty($lstReceiptNum)){
            $setting = [];
            $setting["name"] = self::$CONST_REC_NUM;
            $setting["value"] = $startNum;
            $setting["company_id"] = Auth::user()->company_id;
            $settingCtrl->createLocal([$setting]);
        }else{
            $lstReceiptNum[0]["value"] = $startNum;
            $settingCtrl->upsertLocal($lstReceiptNum);
        }
    }

    /**
     * Method to update invoice after $receipt is completed
     * @param $receipt      receipt record
     */
    public static function doCheckInvoice($receipt){
        //get related invoice to check for update status
        // $lstInvReceipts = InvoiceReceipt::where("invoices_id", );
        // invoices_id, receipts_id, amount
    }

    /**
     * Method for make payment from POS app
     * @param $saleOrder    Object Sale order to make payment
     * @param $lstReceipts  Array object receipts that pay for that sale order
     * @return responseHandler
     * @authro Sopha Pum
     * @createdDate 20-12-2020
     */
    public static function makePaymentPOSReceipt($saleOrder, $lstReceipts){
        return POSHandler::makePayment($saleOrder, $lstReceipts);
    }

    
    /** Function use for create receipt after created person */
    public static function createReceiptFromPerson($person) {

        $controller = new ReceiptController();

        if (isset($person["id"]) && $person["create_receipt"]) {

            $receiptCreate = [
                "received_from_id" => $person["id"],
                "cash_account_id" => $person["create_receipt"]["cash_account_id"],
                "amount" => empty($person["create_receipt"]["amount"]) ? 0 : $person["create_receipt"]["amount"]
            ];
            $controller->createLocal([$receiptCreate]);
        }
    }
}
