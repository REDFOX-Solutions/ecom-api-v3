<?php

namespace App\Model;



class ChartOfAccount extends MainModel
{
    protected $table = 'chart_of_account';
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
        "code", 
        "accounting_name", 
        "accounting_class_id",
        "is_active",
        "is_cash_acc",
        "name",
        "system_default"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer",
        "is_cash_acc" => "integer",
        "system_default" => "integer"
    ];

    protected $appends = [];

    public function glDetails(){
        return $this->hasMany('App\Model\GeneralLedgerDetails', 'coa_id', 'id');
    } 
    public function accountingClass(){
        return $this->belongsTo('App\Model\AccountingClass', 'accounting_class_id');
    }
}
