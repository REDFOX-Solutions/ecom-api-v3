<?php

namespace App\Model;

use App\Services\DatetimeUtils;
use Carbon\Carbon;

class GeneralLedger extends MainModel
{
    protected $table = 'general_ledger';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        "id",
        "created_date",
        "created_by_id",
        "updated_date",
        "updated_by_id",
        "is_backup",
        "module",
        "batch_number", 
        "transaction_date",
        "orig_gl_id",
        "is_reverse",
        "status",
        "comments",
        "note",
        "ledger_id",
        "cash_transfer_id",
        "link_original_batch",
        "released_date",
        "branch_id"
    ];
    protected $appends = ['total_credit', 'total_debit','fin_period'];

    protected $casts = [
        "id" => "string",
        "is_backup" => "integer",
        "is_reverse" => "integer",
        "total_credit" => "double",
        "total_debit" => "double"
    ];

    public static $relationship = [
        "children" => [ 
            [ "name" => "journal_details", "parent_field" => "general_ledger_id", "controller" => "App\Http\Controllers\API\GeneralLedgerDetailsController"]
            
        ],
        "parent" => []
    ];

    public function getTotalCreditAttribute()
    {
        $totalCredit = 0;
        $lstJoCre = GeneralLedgerDetails::where("general_ledger_id", "{$this->id}")
            ->get()
            ->toArray();
        foreach ($lstJoCre as $key => $credit) {
            $totalCredit += $credit["credit_amount"];
        }
        return $totalCredit;
    }

    public function getTotalDebitAttribute()
    {
        $totalDebit = 0;
        $lstJoDe = GeneralLedgerDetails::where("general_ledger_id", "{$this->id}")
            ->get()

            ->toArray();
        foreach ($lstJoDe as $index => $debit) {
            $totalDebit += $debit["debit_amount"];
        }
        return $totalDebit;
    }
    
    public function getFinPeriodAttribute(){
        return isset($this->transaction_date) ? DatetimeUtils::setPeriod($this->transaction_date): null;

    }

    public function journalDetails()
    {
        return $this->hasMany('App\Model\GeneralLedgerDetails', 'general_ledger_id', 'id');
    }

    public function accountingBook()
    {
        return $this->belongsTo('App\Model\AccountingBook', 'ledger_id');
    }

    public function details()
    {
        return $this->hasMany('App\Model\GeneralLedgerDetails', 'general_ledger_id', 'id');
    }
    public function glDetails()
    {
        return $this->hasMany('App\Model\GeneralLedgerDetails', 'general_ledger_id', 'id');
    }
    public function cashTransaction()
    {
        return $this->belongsTo('App\Model\CashTransfer', 'cash_transfer_id');
    }
    public function originalBatch()
    {
        return $this->belongsTo('App\Model\GeneralLedger', 'orig_gl_id');
    }
}
