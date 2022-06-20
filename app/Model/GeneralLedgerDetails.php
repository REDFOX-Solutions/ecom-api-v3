<?php

namespace App\Model;



class GeneralLedgerDetails extends MainModel
{
    protected $table = 'general_ledger_details';
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
        "coa_id", 
        "sub_coa_id", 
        "credit_amount", //-
        "debit_amount", //+
        "transaction_type", 
        "ref_number", 
        "comments", 
        "general_ledger_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "credit_amount" => "double", 
        "debit_amount" => "double" 
    ];

    protected $appends = [];

    public function generalLedger(){
        return $this->belongsTo('App\Model\GeneralLedger', 'general_ledger_id');
    }

    public function chartOfAccount(){
        return $this->belongsTo('App\Model\ChartOfAccount', 'coa_id')->with("accountingClass");
    }

    public function subChartOfAccount(){
        return $this->belongsTo('App\Model\ChartOfAccount', 'sub_coa_id');
    } 
}
