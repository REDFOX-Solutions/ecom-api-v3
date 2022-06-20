<?php

namespace App\Model;

use App\Services\DatetimeUtils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class COATransactionSummary extends MainModel
{
    protected $table = 'coa_transaction_summary'; 
    protected $with = [];
    protected $withCount = []; 

    protected $fillable = [
        "id", 
        "created_date", 
        "created_by_id", 
        "updated_date", 
        "updated_by_id",
        "is_backup",
        "class_type", //expense , cogs, asset, liability, income
        "trans_date", 
        "total_amount",  
        "total_credit",
        "total_debit",
        "coa_id",
        "ledger_id",
        "branch_id",
        "acc_cls_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "total_amount" => "decimal:4",
        "total_credit" => "decimal:4",
        "total_debit" => "decimal:4",
        "begin_balance" => "decimal:4",
        "ending_balance" => "decimal:4"
    ];

    protected $appends = ["begin_balance", "ending_balance","fin_period"];

    /**
     * Begin Balance we will get from the previous transaction day
     * group by Cart Of Acc, Ledger, and Branch
     */
    public function getBeginBalanceAttribute(){
        if(!isset($this->trans_date)){
            return 0;
        }
        $thisTransDate = new Carbon($this->trans_date);

        $lstPrevious = COATransactionSummary::where("coa_id", $this->coa_id)
                                            ->where("ledger_id", $this->ledger_id)
                                            ->where("branch_id", $this->branch_id)
                                            ->whereDate("trans_date", "<", $thisTransDate)
                                            ->orderBy("trans_date", "desc")
                                            ->limit(1)
                                            ->get()
                                            ->toArray();
        return empty($lstPrevious) ? 0 : $lstPrevious[0]["ending_balance"];
    }

    /**
     * get ending balance
     */
    public function getEndingBalanceAttribute(){
        $beginBalance = isset($this->begin_balance) ? $this->begin_balance : 0;
        $credit = isset($this->total_credit) ? $this->total_credit : 0;
        $debit = isset($this->total_debit) ? $this->total_debit : 0;

        return ($beginBalance + $debit) - $credit;
    }
 
    public function getFinPeriodAttribute(){
        return isset($this->trans_date) ? DatetimeUtils::setPeriod($this->trans_date) : null;
    }

    // Relationship
    public function details(){
        return $this->hasMany('App\Model\COATransactionDetail','coa_transaction_summary_id','id');
    }
    public function chartOfAccount(){
        return $this->belongsTo('App\Model\ChartOfAccount','coa_id');
    }
    // child
    public function accountClass(){
        return $this->belongsTo('App\Model\AccountingClass','acc_cls_id');
    }
}
