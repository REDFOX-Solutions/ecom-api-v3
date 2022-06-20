<?php

namespace App\Model;


use App\Model\PurchasePayment;
use App\Model\PurchaseBill;

class PurchaseBillPayment extends MainModel
{
    protected $table = 'purchase_bill_payments';
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
        "purchase_bills_id",
        "purchase_payments_id",
        "amount"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "amount" => "double"
    ];

    public function payments(){
        return $this->belongsTo('App\Model\PurchasePayment', 'purchase_payments_id', 'id')->with(["vendor"]);
    }

    public function purchaseBills(){
        return $this->belongsTo('App\Model\PurchaseBill', 'purchase_bills_id', 'id');
    }
}
