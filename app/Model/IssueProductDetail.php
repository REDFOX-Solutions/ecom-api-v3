<?php

namespace App\Model;
 

class IssueProductDetail extends MainModel
{
    protected $table = 'issue_product_detail';
    protected $keyType = 'string';
    protected $with = ['langs'];
    protected $withCount = [];
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
        "product_id", 
        "trans_uom_id", 
        "base_uom_id", 
        "qty", 
        "location_id", 
        "issue_prod_id",
        "unit_price",
        "unit_cost",
        "discount_amt",
        "discount_rate"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "qty" => "double", 
        "unit_price" => "double",
        "unit_cost" => "double",
        "discount_amt" => "double",
        "discount_rate" => "double",
        "total_cost" => "double"
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
    public function langs(){
        return $this->hasMany('App\Model\IssueProductDetailTranslate', 'issue_prod_detail_id', 'id');
    }

    public function product(){
        return $this->belongsTo('App\Model\Products', 'product_id');
    }

    public function issueProduct(){
        return $this->belongsTo('App\Model\IssueProduct', 'issue_prod_id');
    }
}
