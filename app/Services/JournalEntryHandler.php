<?php

namespace App\Services;

use Carbon\Carbon;
use App\Model\Company;
use App\Model\CashTransfer;
use App\Model\GeneralLedger;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\API\GeneralLedgerController;
use App\Http\Controllers\API\GeneralLedgerDetailsController;
use App\Model\GeneralLedgerDetails;
use App\Model\Invoices;
use App\Model\IssueProduct;
use App\Model\PurchaseBill;
use App\Model\PurchaseBillPayment;
use App\Model\PurchasePayment;
use App\Model\ReceiptProduct;
use App\Model\Receipts;

class JournalEntryHandler
{

    public static function setDefaultValue(&$lstSetValue)
    {

        foreach ($lstSetValue as $key => &$default) {

            //default module = GL
            if (!isset($default['module']) || empty($default["module"])) {
                $default["module"] = 'GL';
            }
            // get current date
            $transferDate = new Carbon();
            // $default["is_reverse"]=false;
            if (empty($default["transaction_date"])) {
                $default["transaction_date"] = $transferDate->format(GlobalStaticValue::$FORMAT_DATETIME);
            }

            if (empty($default["batch_number"]) || $default["batch_number"] == null) {
                $default["batch_number"] = DatabaseGW::generateReferenceCode('general_ledger');
            }
        }
    }
 

    // create Journal Entry from Cash Transfer
    public static function createJEFromCT($cashTransferId, $isVoid = false){ 

        //get ledger id from company to populate in JE
        $ledger_id = CompanyHandler::getCompanyLedgerId();

        $lstCTs = CashTransfer::where("id", $cashTransferId)
                                ->with(["fromCashAccount", "toCashAccount", "receipts", "desbursements"])
                                ->get()
                                ->toArray();

        if(empty($lstCTs)) return;
        $cashTrans = $lstCTs[0];

        $lstReceipts = isset($cashTrans["receipts"]) ? $cashTrans["receipts"] : [];
        $lstDisbursements = isset($cashTrans["desbursements"]) ? $cashTrans["desbursements"] : [];
        $lstNewGLDetails = [];

        
        //if cash transaction is cash entry, we will create detail from receipts and disbursement
        if(strtolower($cashTrans["transfer_type"]) == "cash entry"){
            $mapCashAccCOAs = [];
            $mapProdCOAs = [];
            foreach ($lstReceipts as $key => $cashDetail) {

                if(isset($cashDetail["cash_acc_coa_id"])){
                    $cashAccCOAId = $cashDetail["cash_acc_coa_id"];
                    if(!isset($mapCashAccCOAs[$cashAccCOAId])){
                        $mapCashAccCOAs[$cashAccCOAId] = 0;
                    }
                    $mapCashAccCOAs[$cashAccCOAId] += $cashDetail["amount"];
                }
                
                if(isset($cashDetail["product_coa_id"])){
                    $prodCOAId = $cashDetail["product_coa_id"];
                    if(!isset($mapProdCOAs[$prodCOAId])){
                        $mapProdCOAs[$prodCOAId] = 0;
                    }
                    $mapProdCOAs[$prodCOAId] += $cashDetail["amount"];
                }
            }

            //generate GL Detail for map COA
            foreach ($mapCashAccCOAs as $coaId => $amount) {
                $newGLDetailDebit = $isVoid == true ? self::generateGLCredit($coaId, $amount) : self::generateGLDebit($coaId, $amount);
                $lstNewGLDetails[] = $newGLDetailDebit;
            }

            foreach ($mapProdCOAs as $coaId => $amount) {
                $newGLDetailCredit = $isVoid == true ? self::generateGLDebit($coaId, $amount) : self::generateGLCredit($coaId, $amount);
                $lstNewGLDetails[] = $newGLDetailCredit;
            }
        }

        //if cash transaction is "Fund Transfer" or "bank deposit", 
        //we will create GL detail from From_cash_account and To_Cash_Account
        if(strtolower($cashTrans["transfer_type"]) == "fund transfer" || 
        strtolower($cashTrans["transfer_type"]) == "bank deposit")
        {
            $transferAmount = isset($cashTrans["transfer_amount"]) ? $cashTrans["transfer_amount"] : 0;

            if(isset($cashTrans["to_cash_account"]) && isset($cashTrans["to_cash_account"]["chart_of_acc_id"])){
                $toCOAId = $cashTrans["to_cash_account"]["chart_of_acc_id"];
                $newGLDetailDebit = $isVoid == true ? self::generateGLCredit($toCOAId, $transferAmount) : self::generateGLDebit($toCOAId, $transferAmount);
                $lstNewGLDetails[] = $newGLDetailDebit;
            }

            if(isset($cashTrans["from_cash_account"]) && isset($cashTrans["from_cash_account"]["chart_of_acc_id"])){
                $fromCOAId = $cashTrans["from_cash_account"]["chart_of_acc_id"];
                $newGLDetailCredit = $isVoid == true ? self::generateGLDebit($fromCOAId, $transferAmount) : self::generateGLCredit($fromCOAId, $transferAmount);
                $lstNewGLDetails[] = $newGLDetailCredit;
            }
        }

        //create GL detail from disbursement 
        //for disbusment: product coa = debit, cash acc coa = credit
        if(!empty($lstDisbursements)){
            $mapDisbDebits = [];
            $mapDisbCredits = [];
            foreach ($lstDisbursements as $key => $cashDetail) {

                if(isset($cashDetail["cash_acc_coa_id"])){
                    $cashAccCOAId = $cashDetail["cash_acc_coa_id"];

                    if(!isset($mapDisbCredits[$cashAccCOAId])){
                        $mapDisbCredits[$cashAccCOAId] = 0;
                    }
                    $mapDisbCredits[$cashAccCOAId] += $cashDetail["amount"];
                }
                
                if(isset($cashDetail["product_coa_id"])){
                    $prodCOAId = $cashDetail["product_coa_id"];
                    if(!isset($mapDisbDebits[$prodCOAId])){
                        $mapDisbDebits[$prodCOAId] = 0;
                    }
                    $mapDisbDebits[$prodCOAId] += $cashDetail["amount"];
                }

                //generate GL Detail for map COA
                foreach ($mapDisbDebits as $coaId => $amount) {
                    $newGLDetailDebit = $isVoid == true ? self::generateGLCredit($coaId, $amount) : self::generateGLDebit($coaId, $amount);
                    $lstNewGLDetails[] = $newGLDetailDebit;
                }

                foreach ($mapDisbCredits as $coaId => $amount) {
                    $newGLDetailCredit = $isVoid == true ? self::generateGLDebit($coaId, $amount) : self::generateGLCredit($coaId, $amount);
                    $lstNewGLDetails[] = $newGLDetailCredit;
                }

            }
        }

        $newGL = [
            "module" => "CA",
            "cash_transfer_id" => $cashTrans["id"],
            "transaction_date" => $cashTrans["transfer_date"],
            "ledger_id" => $ledger_id,
            "status" => "hold"//create it as hold and update it later to fire trigger logic
        ];

        if(!empty($lstNewGLDetails)){
            $newGL["journal_details"] = $lstNewGLDetails;
        }
 
        $JEController = new GeneralLedgerController();
        $lstCreatedGLs = $JEController->createLocal([$newGL]);

        //update it so it fire trigger logic
        if(!empty($lstCreatedGLs)){
            foreach ($lstCreatedGLs as $index => &$createdGL) {
                $createdGL["status"] = "posted";
            }
            $JEController->createLocal($lstCreatedGLs);
        }
    }

    /**
     * Method to create Journal Entry and Detail when Issue Product completed
     * @param $issueId      String Issue product id
     * @author Sopha Pum | 23-06-2021
     */
    public static function createJEFromIssueProd($issueId){
        //get ledger id from company to populate in JE
        $ledger_id = CompanyHandler::getCompanyLedgerId();

        $lstIssueProds = IssueProduct::where("id", $issueId)
                                    ->with(["details", "reasonCode"])
                                    ->get()
                                    ->toArray();

        //if invalid issue product id, do nothing
        if(empty($lstIssueProds)) return;
        $issueProd = $lstIssueProds[0];
        $lstIssueDetails = isset($issueProd["details"]) ? $issueProd["details"] : []; 

        //if invalid issue product detail, do nothing
        if(empty($lstIssueDetails)) return;

        //do group chart of account
        $mapInvAccount = [];//to store sum of inventory account 
        $mapCOGSAcc = [];
        foreach ($lstIssueDetails as $key => $issueDetail) {
            $product = $issueDetail["product"];
            $total_cost = isset($issueDetail["total_cost"]) ? $issueDetail["total_cost"] : 0;

            //sum for inventory account
            if(isset($product["inventory_coa_id"])){
                $inventory_coa_id = $product["inventory_coa_id"];

                if(!isset($mapInvAccount[$inventory_coa_id])){
                    $mapInvAccount[$inventory_coa_id] = 0;
                }
                $mapInvAccount[$inventory_coa_id] = $mapInvAccount[$inventory_coa_id] + $total_cost;
            }

            if(isset($product["cogs_coa_id"])){
                $cogs_coa_id = $product["cogs_coa_id"];

                if(!isset($mapCOGSAcc[$cogs_coa_id])){
                    $mapCOGSAcc[$cogs_coa_id] = 0;
                }
                $mapCOGSAcc[$cogs_coa_id] = $mapCOGSAcc[$cogs_coa_id] + $total_cost;
            }
        }

        //prepare GL Detail
        $lstNewGLDetails = [];
        
        //create GL for Inventory Account, Inventory Account always use for Issue from Sale and inventory
        foreach ($mapInvAccount as $invCoaId => $amount) {
            $newGLDetail = self::generateGLCredit($invCoaId, $amount);
            $lstNewGLDetails[] = $newGLDetail;
        }

        //for sale, we will create GL detail from COGS Chart of Account each product
        if($issueProd["source"] == "sale"){
            foreach ($mapCOGSAcc as $cosId => $amount) {
                $newGLDetail = self::generateGLDebit($cosId, $amount);
                $lstNewGLDetails[] = $newGLDetail;
            }

        //for Inventory, create GL detail from Reason code 
        }else if($issueProd["source"] == "inventory" && isset($issueProd["reason_code"])){

            $reasonCode = $issueProd["reason_code"];
            $newGLDetail = self::generateGLDebit($reasonCode["coa_id"], $issueProd["total_cost"]);
            $lstNewGLDetails[] = $newGLDetail;
        }
        

        $newGL = [
            "module" => "AR - Issue Product", 
            "transaction_date" => $issueProd["issue_date"],
            "ledger_id" => $ledger_id,
            "status" => "hold"
        ];

        if(!empty($lstNewGLDetails)){
            $newGL["journal_details"] = $lstNewGLDetails;
        }
 
        $JEController = new GeneralLedgerController();
        $lstCreatedGLs = $JEController->createLocal([$newGL]);

        //update it so it fire trigger logic
        if(!empty($lstCreatedGLs)){
            foreach ($lstCreatedGLs as $index => &$createdGL) {
                $createdGL["status"] = "posted";
            }
            $JEController->createLocal($lstCreatedGLs);
        }

        //TODO: Need to update GL link to Issue Product

    }

    /**
     * Method to create Journal Entry when Receipt Prodcut has completed
     * @param $receiptProdId        String Receipt Product Id
     * @author Sopha Pum | 23-06-2021
     */
    public static function createJEFromReceiptProd($receiptProdId){
        //get ledger id from company to populate in JE
        $ledger_id = CompanyHandler::getCompanyLedgerId();

        $lstReceiptProd = ReceiptProduct::where("id", $receiptProdId)
                                    ->with(["details", "reasonCode"])
                                    ->get()
                                    ->toArray();

        //if invalid issue product id, do nothing
        if(empty($lstReceiptProd)) return;
        $receiptProd = $lstReceiptProd[0];
        $lstDetails = isset($receiptProd["details"]) ? $receiptProd["details"] : []; 

        //if invalid issue product detail, do nothing
        if(empty($lstDetails)) return;

        //do group chart of account
        $mapInvAccount = [];//to store sum of inventory account 
        $mapPOAccrual = [];//to store sum of PO Accrual Account
        foreach ($lstDetails as $key => $receiptDetail) {
            $product = $receiptDetail["product"];
            $inventory_coa_id = isset($product["inventory_coa_id"]) ? $product["inventory_coa_id"] : null;
            $po_accrual_coa_id = isset($product["po_accrual_coa_id"]) ? $product["po_accrual_coa_id"] : null;
            $totalCost = isset($receiptDetail["total_cost"]) ? $receiptDetail["total_cost"] : 0; 

            //sum for inventory account
            if(isset($product["inventory_coa_id"])){
                if(!isset($mapInvAccount[$inventory_coa_id])){
                    $mapInvAccount[$inventory_coa_id] = 0;
                }
                $mapInvAccount[$inventory_coa_id] = $mapInvAccount[$inventory_coa_id] + $totalCost;
            }
            
            //sum for cogs
            if(isset($product["po_accrual_coa_id"])){
                if(!isset($mapPOAccrual[$po_accrual_coa_id])){
                    $mapPOAccrual[$po_accrual_coa_id] = 0;
                }
                $mapPOAccrual[$po_accrual_coa_id] = $mapPOAccrual[$po_accrual_coa_id] + $totalCost;
            }
            
        }

        //prepare GL Detail
        $lstNewGLDetails = [];

        //for Inventory Account, we will create both from Inventory and Purchase
        foreach ($mapInvAccount as $coaId => $amount) {
            $newGLDetail = self::generateGLDebit($coaId, $amount);
            $lstNewGLDetails[] = $newGLDetail;
        }

        //if receipt product from purchase, we create GL by PO Accrual Chart of accoun from each product
        if($receiptProd["soruce"] == "purchase"){
            foreach ($mapPOAccrual as $coaId => $amount) {
                $newGLDetail = self::generateGLCredit($coaId, $amount);
                $lstNewGLDetails[] = $newGLDetail;
            }

        //if receipt product from purchase, we create GL by reason code
        }else if($receiptProd["source"] == "inventory"){
            $reasonCode = $receiptProd["reason_code"];
            $newGLDetail = self::generateGLCredit($reasonCode["coa_id"], $receiptProd["total_cost"]);
            $lstNewGLDetails[] = $newGLDetail;
        }
        

        $newGL = [
            "module" => "AP - Receipt Product", 
            "transaction_date" => $receiptProd["receipt_date"],
            "ledger_id" => $ledger_id,
            "status" => "hold"
        ];

        if(!empty($lstNewGLDetails)){
            $newGL["journal_details"] = $lstNewGLDetails;
        }

        $JEController = new GeneralLedgerController();
        $lstCreatedGLs = $JEController->createLocal([$newGL]);

        //update it so it fire trigger logic
        if(!empty($lstCreatedGLs)){
            foreach ($lstCreatedGLs as $index => &$createdGL) {
                $createdGL["status"] = "posted";
            }
            $JEController->createLocal($lstCreatedGLs);
        }
        //TODO: Need to update GL link to Issue Product
    }

    /**
     * Method to create Journal Entry when invoice has confirmed
     * @param $invoiceId        String Invoice Id
     * @author Sopha Pum | 23-06-2021
     */
    public static function createJEFromInvoice($invoiceId){
        //get ledger id from company to populate in JE
        $ledger_id = CompanyHandler::getCompanyLedgerId();

        $lstInvoices = Invoices::where("id", $invoiceId)
                                    ->with(["details"])
                                    ->get()
                                    ->toArray();

        //if invalid issue product id, do nothing
        if(empty($lstInvoices)) return;
        $invoice = $lstInvoices[0];
        $lstDetails = isset($invoice["details"]) ? $invoice["details"] : [];  

        //if invalid issue product detail, do nothing
        if(empty($lstDetails)) return;

        //do group chart of account
        $mapSaleCOAs = [];//to store sum of sale account  
        foreach ($lstDetails as $key => $invDetail) {
            $product = $invDetail["product"];
            $sale_coa_id = isset($product["sale_coa_id"]) ? $product["sale_coa_id"] : null; 
            $amount = isset($invDetail["total_amount"]) ? $invDetail["total_amount"] : 0;

            //sum for inventory account
            if(isset($product["sale_coa_id"])){
                if(!isset($mapSaleCOAs[$sale_coa_id])){
                    $mapSaleCOAs[$sale_coa_id] = 0;
                }
                $mapSaleCOAs[$sale_coa_id] = $mapSaleCOAs[$sale_coa_id] + $amount;
            } 
            
        }

        //prepare GL Detail
        $lstNewGLDetails = [];
        foreach ($mapSaleCOAs as $coaId => $amount) {
            $newGLDetail = self::generateGLCredit($coaId, $amount);
            $lstNewGLDetails[] = $newGLDetail;
        }
        if(isset($invoice["customer_coa_id"])){
            $newGLDetail = self::generateGLDebit($invoice["customer_coa_id"], $invoice["grand_total"]);
            $lstNewGLDetails[] = $newGLDetail;
        }
        $newGL = [
            "module" => "AR - Invoice", 
            "transaction_date" => $invoice["inv_date"],
            "ledger_id" => $ledger_id,
            "status" => "hold"
        ];

        if(!empty($lstNewGLDetails)){
            $newGL["journal_details"] = $lstNewGLDetails;
        }

        $JEController = new GeneralLedgerController();
        $lstCreatedGLs = $JEController->createLocal([$newGL]);

        //update it so it fire trigger logic
        if(!empty($lstCreatedGLs)){
            foreach ($lstCreatedGLs as $index => &$createdGL) {
                $createdGL["status"] = "posted";
            }
            $JEController->createLocal($lstCreatedGLs);
        }
        //TODO: Need to update GL link to Invoice
    }

    /**
     * Method to create Journal Entry when Invoice Receipt is released
     * @param $receiptId        String receipt Id
     * @return void
     * @author Sopha Pum | 24-06-2021
     */
    public static function createJEFromInvoiceReceipt($receiptId){
        //get ledger id from company to populate in JE
        $ledger_id = CompanyHandler::getCompanyLedgerId();

        $lstPayments = Receipts::where("id", $receiptId)
                                ->with(["invoiceReceipts", "cashAccount"])
                                ->get()
                                ->toArray();
        
        if(empty($lstPayments)) return;
        $payment = $lstPayments[0]; 
        $lstInvReceipts = $payment["invoice_receipts"];

        //if there are bill payment, do nothing
        if(empty($lstInvReceipts)) return;

        //do group chart of account
        $mapARAcc = [];//to store sum of AR account

        foreach ($lstInvReceipts as $key => $receipt) {
            $invoice = $receipt["invoice"];
            $amount = isset($receipt["amount"]) ? $receipt["amount"] : 0;

            //sum for vendor account
            if(isset($invoice["customer_coa_id"])){
                $customer_coa_id = $invoice["customer_coa_id"]; 

                if(!isset($mapARAcc[$customer_coa_id])){
                    $mapARAcc[$customer_coa_id] = 0;
                }
                $mapARAcc[$customer_coa_id] = $mapARAcc[$customer_coa_id] + $amount;
            }
        }

        //prepare GL Detail
        $lstNewGLDetails = []; 

        //GL detail for customer
        foreach ($mapARAcc as $coaId => $amount) {
            $newGLDetail = self::generateGLCredit($coaId, $amount);
            $lstNewGLDetails[] = $newGLDetail;
        }

        //GL detail for cash account
        if(isset($payment["cash_account"])){
            $cashAccount = $payment["cash_account"];
            $newGLDetail = self::generateGLDebit($cashAccount["chart_of_acc_id"], $payment["amount"]);
            $lstNewGLDetails[] = $newGLDetail;
        }

        $newGL = [
            "module" => "AR - Invoice Payment", 
            "transaction_date" => $payment["receipt_date"],
            "ledger_id" => $ledger_id,
            "status" => "hold"
        ];

        if(!empty($lstNewGLDetails)){
            $newGL["journal_details"] = $lstNewGLDetails;
        }

        $JEController = new GeneralLedgerController();
        $lstCreatedGLs = $JEController->createLocal([$newGL]);

        //update it so it fire trigger logic
        if(!empty($lstCreatedGLs)){
            foreach ($lstCreatedGLs as $index => &$createdGL) {
                $createdGL["status"] = "posted";
            }
            $JEController->createLocal($lstCreatedGLs);
        }
        //TODO: Need to update GL link to Invoice
    }

    /**
     * Method to create Journal Entry when Bill has confirmed
     * @param $purchaseBillId        String Purchase Bill Id
     * @return void
     * @author Sopha Pum | 24-06-2021
     */
    public static function createJEFromBill($purchaseBillId){
        
        //get ledger id from company to populate in JE
        $ledger_id = CompanyHandler::getCompanyLedgerId();

        $lstBills = PurchaseBill::where("id", $purchaseBillId)
                                    ->with(["billDetails"])
                                    ->get()
                                    ->toArray();

        //if invalid issue product id, do nothing
        if(empty($lstBills)) return;
        $purchaseBill = $lstBills[0];
        $lstDetails = isset($purchaseBill["bill_details"]) ? $purchaseBill["bill_details"] : [];  

        //if invalid issue product detail, do nothing
        if(empty($lstDetails)) return;

        //do group chart of account
        $mapExpenseAcc = [];//to store sum of expense account  
        $mapPOAccrual = [];//to store sum of PO Accrual

        foreach ($lstDetails as $key => $detail) {

            if(!isset($detail["products"])) continue;

            $product = $detail["products"];
            $cogs_coa_id = isset($product["cogs_coa_id"]) ? $product["cogs_coa_id"] : null;
            $po_accrual_coa_id = isset($product["po_accrual_coa_id"]) ? $product["po_accrual_coa_id"] : null;

            //for calc map
            $amount = isset($detail["amount"]) ? $detail["amount"] : 0;
            
            //sum for expense account
            if(isset($product["cogs_coa_id"])){
                if(!isset($mapExpenseAcc[$cogs_coa_id])){
                    $mapExpenseAcc[$cogs_coa_id] = 0;
                }
                $mapExpenseAcc[$cogs_coa_id] = $mapExpenseAcc[$cogs_coa_id] + $amount;
            } 

            //sum for po accrual
            if(isset($product["po_accrual_coa_id"])){
                if(!isset($mapPOAccrual[$po_accrual_coa_id])){
                    $mapPOAccrual[$po_accrual_coa_id] = 0;
                }
                $mapPOAccrual[$po_accrual_coa_id] = $mapPOAccrual[$po_accrual_coa_id] + $amount;
            } 
            
        }

        //prepare GL Detail
        $lstNewGLDetails = [];

        //if bill is from accounting, we need to use Expense account for debit
        //if bill is from purchase, we need to use PO Accrual Account for debit
        $mapDebitAcc = ($purchaseBill["bill_from"] == "accounting" ? $mapExpenseAcc : $mapPOAccrual);
        foreach ($mapDebitAcc as $coaId => $amount) {
            $newGLDetailDebit = self::generateGLDebit($coaId, $amount);
            $lstNewGLDetails[] = $newGLDetailDebit;
        }
        

        if(isset($purchaseBill["vendor_coa_id"])){
            $newGLDetailCredit = self::generateGLCredit($purchaseBill["vendor_coa_id"], $purchaseBill["total_balance"]);
            $lstNewGLDetails[] = $newGLDetailCredit;
        }

        $newGL = [
            "module" => "AP - Bill", 
            "transaction_date" => $purchaseBill["bill_date"],
            "ledger_id" => $ledger_id,
            "status" => "hold"
        ];

        if(!empty($lstNewGLDetails)){
            $newGL["journal_details"] = $lstNewGLDetails;
        }

        $JEController = new GeneralLedgerController();
        $lstCreatedGLs = $JEController->createLocal([$newGL]);

        //update it so it fire trigger logic
        if(!empty($lstCreatedGLs)){
            foreach ($lstCreatedGLs as $index => &$createdGL) {
                $createdGL["status"] = "posted";
            }
            $JEController->updateLocal($lstCreatedGLs);
        }
        //TODO: Need to update GL link to Invoice
    }

    /**
     * Method to create Journal Entry when Bill Payment is released
     * @param $pBillPaymentId        String Purchase Bill payment Id
     * @return void
     * @author Sopha Pum | 24-06-2021
     */
    public static function createJEFromBillPayment($pBillPaymentId){
        //get ledger id from company to populate in JE
        $ledger_id = CompanyHandler::getCompanyLedgerId();

        $lstPayments = PurchasePayment::where("id", $pBillPaymentId)
                                        ->with(["billPayments", "cashAccounts"])
                                        ->get()
                                        ->toArray();
         
        if(empty($lstPayments)) return;
        $payment = $lstPayments[0]; 
        $lstBillPayments = $payment["bill_payments"];
 
        //if there are bill payment, do nothing
        if(empty($lstBillPayments)) return;

        //do group chart of account
        $mapAPVendorAcc = [];//to store sum of AP account

        foreach ($lstBillPayments as $key => $billPayment) {
            $purchaseBill = $billPayment["purchase_bills"];
            $purBillAmt = isset($billPayment["amount"]) ? $billPayment["amount"] : 0;
            

            //sum for vendor account
            if(isset($purchaseBill["vendor_coa_id"])){
                $vendor_coa_id = $purchaseBill["vendor_coa_id"]; 

                if(!isset($mapAPVendorAcc[$vendor_coa_id])){
                    $mapAPVendorAcc[$vendor_coa_id] = 0;
                }
                $mapAPVendorAcc[$vendor_coa_id] = $mapAPVendorAcc[$vendor_coa_id] + $purBillAmt;
            }
        }

        //prepare GL Detail
        $lstNewGLDetails = []; 

        //GL detail for vendor
        foreach ($mapAPVendorAcc as $coaId => $amount) {
            $newGLDetailDebit = self::generateGLDebit($coaId, $amount);
            $lstNewGLDetails[] = $newGLDetailDebit;
        }
        
        //GL detail for cash account
        if(isset($payment["cash_accounts"])){
            $cashAccount = $payment["cash_accounts"];
            $newGLDetailCredit = self::generateGLCredit($cashAccount["chart_of_acc_id"], $payment["amount"]);
            $lstNewGLDetails[] = $newGLDetailCredit;
        }

        $newGL = [
            "module" => "AP - Bill Payment", 
            "transaction_date" => $payment["payment_date"],
            "ledger_id" => $ledger_id,
            "status" => "hold"
        ];

        if(!empty($lstNewGLDetails)){
            $newGL["journal_details"] = $lstNewGLDetails;
        }

        $JEController = new GeneralLedgerController();
        $lstCreatedGLs = $JEController->createLocal([$newGL]);

        //update it so it fire trigger logic
        if(!empty($lstCreatedGLs)){
            foreach ($lstCreatedGLs as $index => &$createdGL) {
                $createdGL["status"] = "posted";
            }
            $JEController->createLocal($lstCreatedGLs);
        }
        //TODO: Need to update GL link to Invoice
    }
    

    public static function onReversed($lstJournal){
        $listJournals = [];

        foreach ($lstJournal as $key => $lstJe) {
            $original_batch_id = $lstJe["orig_gl_id"];
            if (!empty($original_batch_id) && $lstJe["is_reverse"] == 1) {
                $reverse = GeneralLedger::where("id", "{$original_batch_id}")
                    ->get()->toArray();
                // throw new CustomException("list that  Reverse",$reverse);

                foreach ($reverse as $index => $JE) {
                    if (!empty($JE["status"]) && $JE["status"] = "posted") {

                        $JE["status"] = "reversed";
                    }
                    $listJournals[] = $JE;
                }
            }

            $controller = new GeneralLedgerController();
            $controller->createLocal($listJournals);
        }
    }
    // delete general ledger detail when delete gl
    public static function deleteGlDetail($glIds){
        $lstGlDetailId = [];

        $lstGlDetail = GeneralLedgerDetails::where('general_ledger_id', $glIds)->get()->toArray();
        if (!empty($lstGlDetail)) {
            foreach ($lstGlDetail as $key => $gl_detail) {
                # code...
                $lstGlDetailId[] = [
                    "id" => $gl_detail["id"]
                ];
            }
            $con = new GeneralLedgerDetailsController();
            $con->delete($lstGlDetailId);
        }
    }

    /**
     * Method to generate GL Detail value in debit
     * @param $coaId        String Chart of Account Id
     * @param $amount       Double Amount
     * @author Sopha Pum | 25-06-2021
     */
    private static function generateGLDebit($coaId, $amount){
        $newGLDetailDebit = [
            "coa_id" => $coaId,
            "credit_amount" => 0,
            "debit_amount" => $amount
        ];
        return $newGLDetailDebit;
    }

    /**
     * Method to generate GL Detail value in credit
     * @param $coaId        String Chart of Account Id
     * @param $amount       Double Amount
     * @author Sopha Pum | 25-06-2021
     */
    private static function generateGLCredit($coaId, $amount){
        $newGLDetailCredit = [
            "coa_id" => $coaId,
            "credit_amount" => $amount,
            "debit_amount" => 0
        ];
        return $newGLDetailCredit;
    }
}
