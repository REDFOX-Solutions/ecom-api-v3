<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\RestResource;
use App\Model\Invoices;
use App\Model\Shipment;
use App\Services\DatabaseGW;
use App\Services\InvoiceHandler;
use Illuminate\Http\Request;

class InvoiceController extends RestAPI
{
    protected $CONST_INV_NUM = "invoice_num";

    public function getTableSetting(){
        return [
            'tablename' => 'invoices',
            'model' => 'App\Model\Invoices', 
            'prefixId' => 'inv',
            'modelTranslate' => 'App\Model\InvoiceTranslation',
            'prefixLangId' => 'inv0t',
            'parent_id' => 'invoices_id'
        ];
    }

    public function getQuery(){
        return Invoices::query();
    }
    
    public function getModel(){
        return 'App\Model\Invoices';
    }
    
    public function getCreateRules(){
        return [ 
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }

    public function beforeCreate(&$lstNewRecords){
        //add invoice number for created invoice
        InvoiceHandler::setupDefaultFieldsForCreate($lstNewRecords);
        InvoiceHandler::addInvNumber($lstNewRecords);   
    }
    
    public function afterCreate(&$lstNewRecords){ 
        foreach ($lstNewRecords as $key => $newInvoice) {
            if(isset($newInvoice["status"]) && $newInvoice["status"] == 'confirmed'){

                JournalEntryHandler::createJEFromInvoice($newInvoice["id"]);
            }
        }
    }
    
    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[])
    {

        //TODO: Check to update Invoice status
        //if invoice changed amount_paid or grand_total, we will run the criteria as below
            //if Yes, check if amount paid == grand_total
                //if Yes, it means invoice is completed => update invoice status = "completed"
                //if No, do nothing
            //if No, do nothing
            foreach($lstNewRecords as $key => &$newInvoice){
                $oldInvoice = $mapOldRecords[$newInvoice["id"]];
    
                if(isset($newInvoice["status"]) && 
                    $oldInvoice["status"] != $newInvoice["status"] &&
                    $newInvoice["status"] == "confirmed")
                {
                    InvoiceHandler::doBeforeRelease($newInvoice, $oldInvoice);
                }
            }
        
        
    }
    
    public function afterUpdate(&$lstNewRecords, $mapOldRecords=[]){

        InvoiceHandler::doUpdateShipmentAfterUpdateInv($lstNewRecords, $mapOldRecords);

        
        foreach ($lstNewRecords as $key => $newInvoice) {
            $oldInvoice = $mapOldRecords[$newInvoice["id"]];

            //apply logic when invoice updated to confirmed here
            if(isset($newInvoice["status"]) && 
                $oldInvoice["status"] != $newInvoice["status"] &&
                $newInvoice["status"] == 'confirmed')
            {
                JournalEntryHandler::createJEFromInvoice($newInvoice["id"]);
            }
        }
    }

    public function afterDelete($lstOldRecords){ 
    }

    public function getInvoiceBySaleOrder(Request $request){
        try{
            $lstFilters = $request->all();

            if(!isset($lstFilters["sale_order_id"])) return $this->clientError("Invalid Sale Order Id!");

            $saleOrderID = $lstFilters["sale_order_id"];
 
            $lstInvIds = [];
            Shipment::where("sales_order_id", $saleOrderID)->get()->each(function($item) use (&$lstInvIds){
                if(!empty($item["invoices_id"])){
                    $lstInvIds[] = $item["invoices_id"];
                }
            });
 
            $filterInv = [
                "id" => implode(',', $lstInvIds) 
            ]; 

            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $filterInv));

        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
        
    }
}
