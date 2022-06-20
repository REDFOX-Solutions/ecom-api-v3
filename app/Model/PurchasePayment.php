<?php

namespace App\Model;



class PurchasePayment extends MainModel
{
    protected $table = 'purchase_payments';
    protected $keyType = 'string';
    public $incrementing = false; 
    protected $with = ["langs"];
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
        "received_by_id",
        "amount",  
        "payment_num",
        "pay_by_id",
        "payment_date",
        "payment_type",
        "vendor_id",
        "status", 
        "payment_method",
        "cash_acc_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "amount" => "double"
    ];

    public static $relationship = [
        "children" => [ 
            ["name" => "bill_payments", "parent_field" => "purchase_payments_id", "controller" => "App\Http\Controllers\API\PurchaseBillPaymentController"]
        ],
        "parent" => []
    ];

    protected $appends = [];



    public function langs(){
        return $this->hasMany('App\Model\PurchasePaymentTranslation', 'purchase_payment_id', 'id');
    }

    public function vendor(){
        return $this->belongsTo('App\Model\PersonAccount', 'vendor_id', 'id');
    }

    public function users(){
        return $this->belongsTo('App\Model\User', 'received_by_id');
    }

    public function receiptBy(){
        return $this->belongsTo('App\Model\PersonAccount', 'received_by_id');
    }

    public function cashAccounts(){
        return $this->belongsTo('App\Model\CashAccounts', 'cash_acc_id');
    }

    public function billPayments(){
        return $this->hasMany('App\Model\PurchaseBillPayment','purchase_payments_id', 'id')->with("purchaseBills");
    }
 
}
