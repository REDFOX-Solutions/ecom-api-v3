<?php

namespace App\Services;

use App\Http\Controllers\API\InvoiceController;
use App\Model\Invoices;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\API\InvoiceReceiptController;

class InvoiceReceiptHandler
{

    // public static function recalc
    /**
     * Method to update invoice amount_paid after record created
     * and check for update status
     * @param $lstNewInvRec   lst new invoice receipt object record
     */
    public static function updateInvs($lstNewInvRec){
 
        
        //to get all invoices to calc status
        $lstInvIds = [];
        foreach ($lstNewInvRec as $key => $newInvRec) {
            $lstInvIds[] = $newInvRec["invoices_id"];
        }

        //get all invoice receipt to update invoice amount
        $lstExistedInvoices = Invoices::whereIn("id", $lstInvIds)
                            ->with("invoiceReceipts")
                            ->get()
                            ->toArray();

        //do SUM all amount from receipt that related invoice,
        //to update invoice
        $mapSumInvAmount = [];
        foreach ($lstExistedInvoices as $index => $existedInv) {

            $lstInvReceipts = $existedInv["invoice_receipts"];
            $keyInvId = $existedInv["id"];

            foreach ($lstInvReceipts as $subIndx => $invReceipt) {
                if(!isset($mapSumInvAmount[$keyInvId])){
                    $mapSumInvAmount[$keyInvId] = 0;
                }

                $oldAmount = $mapSumInvAmount[$keyInvId];
                $newAmount = isset($invReceipt["amount"]) ? $invReceipt["amount"] : 0;
                $mapSumInvAmount[$keyInvId] = $newAmount + $oldAmount;
            }
        }

        $lstUpdatedInvoices = [];
        foreach ($lstExistedInvoices as $key => $existedInv) {
            $keyInvId = $existedInv["id"];
            $amount_paid = isset($mapSumInvAmount[$keyInvId]) ? $mapSumInvAmount[$keyInvId] : 0;
            $newInv = [
                "id" => $existedInv["id"],
                "amount_paid" => $amount_paid,
                "status" => ($existedInv["grand_total"] <= $amount_paid ? "closed" : $existedInv["status"])
            ];
            $lstUpdatedInvoices[] = $newInv;
        }
        
        if(!empty($lstUpdatedInvoices)){
            $invCtrler = new InvoiceController();
            $invCtrler->updateLocal($lstUpdatedInvoices);
        }
        
    }

    
    /** Create Invoice Receipt */
    public static function createInvoiceReceipt($lstReceipts) {
        foreach ($lstReceipts as $key => $receipt) {
            
            $lstInvReceipts = [];

            if ( isset($receipt["invoice_receipts"]) && !empty($receipt["invoice_receipts"])) {
                
                foreach ($receipt["invoice_receipts"] as $key => &$invoiceReceipt) {
                    $invoiceReceipt["receipts_id"] = $receipt["id"];
                    $lstInvReceipts[] = $invoiceReceipt;
                }

                $invoiceReceiptCtrl = new InvoiceReceiptController();
                $invoiceReceiptCtrl -> createLocal($lstInvReceipts);
            }

        }

    }


    /** Create invoice receipt */
    public static function createInvoiceReceipts($lstReceipts) {
        foreach ($lstReceipts as $key => $receipt) {

            $lstInvoiceReceipt = [];
            $receiptAmt = isset($receipt["amount"]) ? $receipt["amount"] : 0;

            if (isset($receipt["invoices"]) && !empty($receipt["invoices"])) {

                foreach ($receipt["invoices"] as $inv => &$invoice) {

                    $grandTotal = isset($invoice["grand_total"]) && !is_null($invoice["grand_total"]) ? $invoice["grand_total"] : 0;
                    $dueBalance = isset($invoice["due_balance"]) && !is_null($invoice["due_balance"]) ? $invoice["due_balance"] : $grandTotal;
                    $invoiceReceiptAmount = 0;

                    if ($receiptAmt >= $dueBalance) {
                        $invoiceReceiptAmount = $dueBalance;
                        $receiptAmt -= $invoiceReceiptAmount;
                    } else {
                        $invoiceReceiptAmount = $receiptAmt;
                    }

                    if (isset($invoiceReceiptAmount) && $invoiceReceiptAmount > 0) {

                        $invoiceReceipt = [
                            "receipts_id" => $receipt["id"],
                            "invoices_id" => $invoice["id"],
                            "amount" => $invoiceReceiptAmount
                        ];
        
                        $lstInvoiceReceipt [] = $invoiceReceipt;
                    }
                }

                $invoiceReceiptCtrl = new InvoiceReceiptController();
                $invoiceReceiptCtrl -> createLocal($lstInvoiceReceipt);
            }
        }
    }
}
