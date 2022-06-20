<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReportSaleByChannel extends MainModel
{
    //
    
    protected $table = 'rpt_sale_by_channel';
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
        "sale_hour", 
        "total_pax", 
        "total_qty",
        "channel",
        "sub_amt",
        "discount_amt",
        "vat_amt",
        "total_amt",
        "sale_summary_id",
        "total_cost"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "total_qty" => "integer", 
        "total_amt" => "double",
        "sub_amt" => "double",
        "discount_amt" => "double",
        "vat_amt" => "double",
        "total_pax" => "integer",
        "total_cost" => "double"
    ]; 

}
