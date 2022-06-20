<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReceiptProduct extends MainModel
{
    protected $table = 'receipt_product';
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
        "ref_code", 
        "external_ref_code", 
        "receipt_date", 
        "status", 
        "post_period", 
        "reason_code_id", 
        "total_qty", 
        "total_cost", 
        "warehouse_id", 
        "source",
        "transfer_prod_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer"
    ];
    /** relational table */

    
    public function details(){
        return $this->hasMany('App\Model\ReceiptProductDetail', 'receipt_prod_id', 'id')->with("product");
    }

    public function reasonCode(){
        return $this->belongsTo('App\Model\ReasonCode', "reason_code_id");
    }
     
}
