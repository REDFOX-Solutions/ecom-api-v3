<?php

namespace App\Model;



class SaleOrderDetail extends MainModel
{
    protected $table = 'sale_order_details';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs'];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id",
		"created_by_id",
		"updated_by_id",
		"created_date",
		"updated_date",
		"is_backup",
		"products_id",
		"is_free",
		"warehouse_id",
		"uom_id",
		"quantity",
		"unit_price",
		"discount_code",
		"discount_rate",
		"discount_amount",  
		"tax_category_id",
        "project_task_id",
        "sales_order_id",
        "shipped_qty",
        "ordering",
        "status",
        "unit_cost",
        "service_date",
        "pos_key",
        "in_transit"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "is_free" => "integer",
        "quantity" => "integer",
        "unit_price" => "double",
        "discount_rate" => "double",
        "discount_amount" => "double", 
        "shipped_qty" => "integer",
        "ordering" => "integer",
        "amount" => "double",
        "total_amount" => "double",
        "unit_cost" => "double",
        "in_transit" => "decimal:4"
    ];

    protected $appends = ["amount", "total_amount"];

    public function getAmountAttribute(){
        $qty = isset($this->quantity) ? $this->quantity : 1;
        $unitPrice = isset($this->unit_price) ? $this->unit_price : 0;
        $subAmt = $qty * $unitPrice;
        return $subAmt;
    }

    public function getTotalAmountAttribute(){
        $qty = isset($this->quantity) ? $this->quantity : 1;
        $unitPrice = isset($this->unit_price) ? $this->unit_price : 0;
        $subAmt = $qty * $unitPrice;
        $disRate = isset($this->discount_rate) ? $this->discount_rate : 0;
        $disPrice = isset($this->discount_amount) ? $this->discount_amount : ($subAmt * (1-$disRate));
        return $subAmt - $disPrice;
    }
 
    public function langs(){
        return $this->hasMany('App\Model\SaleOrderDetailTranslation', 'sale_order_details_id', 'id');
    }

    public function product(){
        return $this->belongsTo('App\Model\Products', 'products_id')->with("category");
    }

    public function saleOrder(){
        return $this->belongsTo('App\Model\SaleOrder', 'sales_order_id');
    }
 
}
