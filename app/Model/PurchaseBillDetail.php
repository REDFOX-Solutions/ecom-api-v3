<?php

namespace App\Model;



class PurchaseBillDetail extends MainModel
{
    protected $table = 'purchase_bill_details';
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
        "bill_id",  
        "product_id",
        "unit_cost",
        "service_date",
        "discount_amount",
        "discount_rate",
        "pr_id",
        "qty"

    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "qty" => "double",
        "unit_cost" => "double",
        "discount_rate" => "double"
    ];

    protected $appends = ["amount"];

    public function getAmountAttribute(){
        $qty = isset($this->qty) ? $this->qty : 1;
        $unitPrice = isset($this->unit_cost) ? $this->unit_cost : 0;
        $subAmt = $qty * $unitPrice;
        $disRate = isset($this->discount_rate) ? $this->discount_rate : 0;
       
        $disPrice = $subAmt * $disRate;
        // $disPrice = isset($this->discount_amount) ? $this->discount_amount : ($subAmt * (1-$disRate));
        return $subAmt - $disPrice;
    }

    public function langs(){
        return $this->hasMany('App\Model\PurchaseBillDetailTranslation', 'bill_detail_id', 'id');
    }

    public function products(){
        return $this->belongsTo('App\Model\Products', 'product_id');
    }

    public function purchaseBill(){
        return $this->belongsTo('App\Model\PurchaseBill', 'bill_id');
    }
}
