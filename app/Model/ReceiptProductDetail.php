<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReceiptProductDetail extends MainModel
{
    protected $table = 'receipt_product_detail';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = 
    [
        "id", 
        "created_by_id", 
        "created_date", 
        "updated_by_id", 
        "updated_date", 
        "is_backup", 
        "receipt_prod_id", 
        "product_id", 
        "trans_uom_id", 
        "base_uom_id", 
        "location_id", 
        "qty",
        "unit_cost",
        "discount_amount",
        "discount_rate"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "qty" => "double",
        "unit_cost" => "double"
    ];

    protected $appends = ['total_cost'];

    public function getTotalCostAttribute(){
        $qty = isset($this->qty) ? $this->qty : 1;
        $unitCost = isset($this->unit_cost) ? $this->unit_cost : 0;
        $costAmt = $qty * $unitCost;
        $disAmt = isset($this->discount_amount) ? $this->discount_amount : 0;

        return $costAmt - $disAmt;
	}

    /** relational table */
    public function product(){
        return $this->belongsTo('App\Model\Products', 'product_id');
    }

    public function receiptProduct(){
        return $this->belongsTo('App\Model\ReceiptProduct', 'receipt_prod_id');
    }
}
