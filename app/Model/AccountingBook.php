<?php

namespace App\Model;



class AccountingBook extends MainModel
{
    protected $table = 'accounting_book';
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
        "name", 
        "code", 
        "ledger_type", //budget, actual
        "comments"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer"
    ];

    protected $appends = [];

    public function generalLedgers(){
        return $this->hasMany('App\Model\GeneralLedger', 'ledger_id', 'id');
    }
    
}
