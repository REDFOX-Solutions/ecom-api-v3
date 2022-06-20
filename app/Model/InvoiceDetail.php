<?php

namespace App\Model;



class InvoiceDetail extends MainModel
{
    protected $table = 'invoice_details';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ["langs"];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id", "created_by_id", "updated_by_id", "created_date", "updated_date", "is_backup", 
        "invoices_id", 
        "qty", 
        "unit_price", 
        "cost",
        "products_id", 
        "service_date",
        "shipment_id",
        "discount_amount",
        "discount_rate"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "qty" => "integer",
        "unit_price" => "double",
        "cost" => "double",
        "discount_amount" => "double",
        "discount_rate" => "double"
    ];

    protected $appends = ['amount', 'total_amount'];

	public function getAmountAttribute(){
        $qty = isset($this->qty) ? $this->qty : 0;
        $unitPrice = isset($this->unit_price) ? $this->unit_price : 0;

        return $qty * $unitPrice;
    }
    
    public function getTotalAmountAttribute(){
        $qty = isset($this->qty) ? $this->qty : 0;
        $unitPrice = isset($this->unit_price) ? $this->unit_price : 0;
        $disAmt = isset($this->discount_amount) ? $this->discount_amount : 0;

        return ($qty * $unitPrice) - $disAmt;
	}

    public function invoice(){
        return $this->belongsTo('App\Model\Invoices', 'invoices_id');
    }
    public function invoiceCustomer(){
        return $this->belongsTo('App\Model\Invoices', 'invoices_id')->with(['shipTo', 'billTo']);
    }

    public function item(){
        return $this->belongsTo('App\Model\Products', 'products_id');
    } 

    public function product(){
        return $this->belongsTo('App\Model\Products', 'products_id');
    }


    public function langs(){
        return $this->hasMany('App\Model\InvoiceDetailsTranslation', 'invoice_details_id', 'id');
    }
}
