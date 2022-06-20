<?php

namespace App\Model;



class PrintInvoiceHistory extends MainModel
{
    protected $table = 'printed_invoice_history';
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
        "sales_order_id",
        "sub_total",
        "ordering",
        "print_datetime"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "sub_total" => "double",
        "ordering" => "integer"
    ];

    protected $appends = [];

    public function saleOrder(){
        return $this->belongsTo('App\Model\SaleOrder', 'sales_order_id');
    }
}
