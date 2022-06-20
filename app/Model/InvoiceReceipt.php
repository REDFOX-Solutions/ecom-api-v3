<?php

namespace App\Model;



class InvoiceReceipt extends MainModel
{
    protected $table = 'invoice_receipt';
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
        "invoices_id",
        "receipts_id",  
        "amount"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "amount" => "double"
    ];
 
    public function invoice(){
        return $this->belongsTo('App\Model\Invoices', 'invoices_id');
    }

    public function receipt(){
        return $this->belongsTo('App\Model\Receipts', 'receipts_id')->with("receivedFrom", "receivedBy");
    } 
 
}
