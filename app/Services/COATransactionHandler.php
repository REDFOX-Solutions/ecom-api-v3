<?php

namespace App\Services;

use App\Http\Controllers\API\COATransactionDetailController;
use App\Http\Controllers\API\COATransactionSummaryController;
use App\Model\ChartOfAccount;
use App\Model\COATransactionSummary;
use App\Model\GeneralLedgerDetails;

class COATransactionHandler 
{

    /**
     * Method to check coa transaction summary when create COA transaction detail
     * If there are no transaction summary, we will create a new one and assign to detail
     * @param $transDetail      COA transaction detail record
     */
    public static function checkTransSummary(&$transDetail){
        $transDate = $transDetail["trans_date"];
        $ledgerId = $transDetail["ledger_id"];
        $branchId = $transDetail["branch_id"];
        $coaId = $transDetail["coa_id"];

        //limit 1 because record summary will have only 1 for  COA, Ledger, Branch, Transaction Date
        $lstSummaries = COATransactionSummary::where("coa_id", $coaId)
                                            ->where("ledger_id", $ledgerId)
                                            ->where("branch_id", $branchId)
                                            ->whereDate("trans_date", $transDate) 
                                            ->limit(1)
                                            ->get()
                                            ->toArray();

        //if there are no summary, we will create a new summary 
        if(empty($lstSummaries)){
            $lstCOAs = ChartOfAccount::where("id", $coaId)->get()->toArray();

            $newSummary = [
                "coa_id" => $coaId,
                "ledger_id" => $ledgerId, 
                "branch_id" => $branchId,
                "trans_date" => $transDate,
                "acc_cls_id" => $lstCOAs[0]["accounting_class_id"],
                "langs" => ["en" => ["comments" => $lstCOAs[0]["name"]]]
            ];
            $summaryCtrler = new COATransactionSummaryController();
            $lstSummaries = $summaryCtrler->createLocal([$newSummary]);
        }

        //assign parent to detail
        $transDetail["coa_transaction_summary_id"] = $lstSummaries[0]["id"];
    }
    
    /**
     * Method log COA transaction detail from GL
     */
    public static function logCOAFromGL($releasedGL){

        // $transDate = $releasedGL["transaction_date"];
        //get GL details to create transaction detail
        $lstGLDetails = GeneralLedgerDetails::where("general_ledger_id", $releasedGL["id"])
                                            ->with("chartOfAccount")
                                            ->get()
                                            ->toArray();

        $lstNewTransDetails = [];
        foreach ($lstGLDetails as $key => $glDetail) {
            $newTransDetail = [
                "coa_id" => $glDetail["coa_id"], 
                "credit_amount" => $glDetail["credit_amount"], 
                "gl_detail_id" => $glDetail["id"], 
                "debit_amount" => $glDetail["debit_amount"], 
                "trans_date" => $releasedGL["transaction_date"] ?? null,
                "acc_cls_id" => $glDetail["chart_of_account"]["accounting_class_id"] ?? null, 
                "ledger_id" => $glDetail["chart_of_account"]["ledger_id"] ?? null, 
                "branch_id" => $glDetail["chart_of_account"]["branch_id"] ?? null 
                
            ];
            $lstNewTransDetails[] = $newTransDetail;
        }

        if(!empty($lstNewTransDetails)){
            $transDetailCtrler = new COATransactionDetailController();
            $transDetailCtrler->createLocal($lstNewTransDetails);
        } 
    }

    /**
     * Method recalculate COA Transaction Summary
     * @param $lstNewTransDetails   Array COA Transaction Detail created new 
     * @return void
     */
    public static function recalcTransSummary($lstNewTransDetails){

        //map transaction summary to easy recalculate 
        $mapSummaryDetail = collect($lstNewTransDetails)->mapToGroups(function($item, $key){
            return [$item["coa_transaction_summary_id"] => $item];
        })->toArray();

        $tranSummaryIds = array_keys($mapSummaryDetail);

        $lstSummaries = COATransactionSummary::whereIn("id", $tranSummaryIds)
                                                ->get()
                                                ->toArray();

        foreach ($lstSummaries as $key => &$tranSummary) {
            $lstDetails = $mapSummaryDetail[$tranSummary["id"]];

            foreach ($lstDetails as $key => $tranDetail) {
                $tranSummary["total_credit"] += $tranDetail["credit_amount"];
                $tranSummary["total_debit"] += $tranDetail["debit_amount"];
            }
        }

        if(!empty($lstSummaries)){
            $tranSumCtrler = new COATransactionSummaryController();
            $tranSumCtrler->createLocal($lstSummaries);
        }
    }

}
 