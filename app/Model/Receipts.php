<?php

namespace App\Model;



class Receipts extends MainModel
{
    protected $table = 'receipts';
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
        "received_from_id", 
        "amount", 
        "receipt_num", 
        "received_by_id",   
        "location_id", 
        "receipt_date", 
        "payment_method",//TBD
        "bank_id",
        "payment_method_id",//TBD
        "status", //hold, open, closed
        "cash_account_id",
        "amount_base_currency",
        "receipt_type"//payment, repay, prepayment
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "receipt_num" => "integer",
        "amount" => "double",
        "amount_base_currency" => "double"
    ];

    public function location(){
        return $this->belongsTo('App\Model\Locations', 'location_id')->with("parent");
    }
    
    public function receivedFrom(){
        return $this->belongsTo('App\Model\PersonAccount', 'received_from_id');
    }

    public function receivedBy(){
        return $this->belongsTo('App\Model\User', 'received_by_id');
    } 
    public function paymentMethod(){
        return $this->belongsTo('App\Model\PaymentMethod', 'payment_method_id');
    } 
    
    // public function bank(){
    //     return $this->belongsTo('App\Model\BankInfo', 'bank_id');
    // } 


    public function invoiceReceipt(){
        return $this->hasMany('App\Model\InvoiceReceipt', 'receipts_id', 'id')->with("invoice");
    }

    public function langs(){
        return $this->hasMany('App\Model\ReceiptTranslation', 'receipts_id', 'id');
    }
}
