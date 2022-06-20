<?php

namespace App\Model;

class AccountingClass extends MainModel
{
    protected $table = 'accounting_class';
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
        "class_type",//asset, liability, expense, income
        "is_active",
        "code",
        "ordering"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer",
        "ordering" => "integer"
    ];

    protected $appends = [];

    public function chartOfAccounts(){
        return $this->hasMany('App\Model\ChartOfAccount', 'accounting_class_id', 'id');
    }
}
