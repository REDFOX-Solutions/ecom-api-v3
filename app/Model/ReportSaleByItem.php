<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSaleByItem extends MainModel
{
    //
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
        "total_cost"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "total_qty" => "integer", 
        "total_amount" => "double",
        "sub_amt" => "double",
        "total_discount" => "double",
        "unit_price" => "double",
        "total_cost" => "double"
    ]; 
    public function category(){
        return $this->belongsTo('App\Model\Categories','category_id');
    } 
}
