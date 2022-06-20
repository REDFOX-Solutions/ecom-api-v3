<?php

namespace App\Model;



class PurchaseOrderDetail extends MainModel
{
    protected $table = 'po_details';
    protected $keyType = 'string';
    public $incrementing = false;
    // protected $with = [];
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
        "product_id",
        "qty",  
        "cost",
        "discount_rate",
        "purchase_orders_id",
        "pr_qty",
        "open_qty",
        "in_transit",
        "uom_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "qty" => "double",
        "cost" => "double",
        "discount_rate" => "double",
        "pr_qty" => "double",
        "open_qty" => "double",
        "in_transit" => "double",
        "amount" => "double"
    ];
    protected $appends = ["amount"];

    public function getAmountAttribute(){
        $qty = isset($this->qty) ? $this->qty : 1;
        $unitPrice = isset($this->cost) ? $this->cost : 0;
        $subAmt = $qty * $unitPrice;
        $disRate = isset($this->discount_rate) ? $this->discount_rate : 0;
       
        $disPrice = $subAmt * $disRate;
        // $disPrice = isset($this->discount_amount) ? $this->discount_amount : ($subAmt * (1-$disRate));
        return $subAmt - $disPrice;
    }
    public function langs(){
        return $this->hasMany('App\Model\PurchaseOrderDetailTranslation', 'po_details_id', 'id');
    }

    public function product(){
        return $this->belongsTo('App\Model\Products', 'product_id');
    }

    public function receiptDetail(){
        return $this->hasMany('App\Model\PurchaseReceiptDetail', 'po_detail_id', 'id');
    }

    public function purchaseOrder(){
        return $this->belongsTo('App\Model\PurchaseOrder', 'purchase_orders_id')->with("purchase_by");
    }
}
