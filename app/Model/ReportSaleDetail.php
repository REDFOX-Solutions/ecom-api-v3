<?php

namespace App\Model;



class ReportSaleDetail extends MainModel
{
    protected $table = 'rpt_sale_by_item';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
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
        "product_id", 
        "category_id", 
        "total_qty", 
        "total_amount",
        "total_discount",
        "unit_price",
        "sale_hour",
        "product_name",
        "category_name",
        "pricebook_name",
        "pricebook_id",
        "sub_amt",
        "uom_id",
        "sale_summary_id",
        "sale_time"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "total_qty" => "integer", 
        "unit_price" => "double",
        "total_amount" => "double",
        "total_discount" => "double",
        "sub_amt" => "double"
    ]; 

    public function product(){
        return $this->belongsTo('App\Model\Products', 'product_id');
    }

}
