<?php

namespace App\Services;
 
use App\Http\Controllers\API\InvoiceController; 
use App\Model\InvoiceDetail; 
use App\Model\PersonAccount; 

class InvoiceHandler
{
    protected static $CONST_INV_NUM = "invoice_num";
    protected static $INV_PREFIX = "invoice_prefix";

    /**
     * Method to generate invoice number 
     * @param $lstOrders    array order just created
     * @return void
     */
    public static function setupDefaultFieldsForCreate(&$lstInvoices){
 
        foreach ($lstInvoices as $key => &$inv) {  
            $inv["invoice_num"] = DatabaseGW::generateReferenceCode("invoices"); 
        }
 
    }

    /**
     * Method to apply all logic before invoice change status to confirmed
     * @param $newInvoice       new record updated
     * @param $oldInvoice       old record in DB
     * @author Sopha Pum | 09-06-2021
     */
    public static function doBeforeRelease(&$newInvoice, $oldInvoice){

        //add customer chart of account to invoice, to avoid customer change chart of acc before we are do payment
        if(isset($newInvoice["bill_to_id"]) || isset($oldInvoice["bill_to_id"])){
            $customerId = isset($newInvoice["bill_to_id"]) ? $newInvoice["bill_to_id"] : $oldInvoice["bill_to_id"];
            $lstCustomers = PersonAccount::where("id", $customerId)->get()->toArray();
            $customer = $lstCustomers[0];

            $newInvoice["customer_coa_id"] = isset($customer["personal_coa_id"]) ? $customer["personal_coa_id"] : null;
        }

    }

    /**
     * When invoice completed, we will update shipment to completed
     * @param $newInv   new invoice object
     */
    public static function doUpdateShipmentAfterUpdateInv($lstNewRecords, $mapOldRecords){

        

        $lstInvCompletedIds = [];
        //logic for invoice changed to completed
        foreach ($lstNewRecords as $index => $newInv) {
            $oldInv = $mapOldRecords[$newInv["id"]];

            //This is the logic for invoice changed to closed
            if(isset($newInv["status"]) &&  
                !empty($newInv["status"]) && 
                $oldInv["status"] != $newInv["status"] && 
                $newInv["status"] == 'closed')
            {
                $lstInvCompletedIds[] = $newInv["id"]; 
            }
        }

        if(!empty($lstInvCompletedIds)){

            //do update shipment after invoice completed
            ShipmentHandler::updateShipmentOnInvCompleted($lstInvCompletedIds);

            //log sale revenue
            
        }
        

    }
    /** Function use to recalculate invoice total */
    public static function reCalInvoicesTotal($invoiceId){
        $grandTotal = 0;
        /** Get all invoice records and recalculate grand total */
        $lstExistingInvoicesDetail = InvoiceDetail::where("invoices_id", $invoiceId)->get()->toArray();
        if (isset($lstExistingInvoicesDetail) && !empty($lstExistingInvoicesDetail)) {
            foreach ($lstExistingInvoicesDetail as $index => $invoicesDetail) {
                /** Get total invoice from invoice detail */ 
                $grandTotal += isset($invoicesDetail["total_amount"]) ? $invoicesDetail["total_amount"]: 0;
            }
        }

        /** Update invoice */
        $invoice = [
            "id" => $invoiceId,
            "grand_total" => $grandTotal
        ];

        $controller = new InvoiceController();
        $controller->updateLocal([$invoice]);
    }
}
