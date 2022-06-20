<?php

namespace App\Model;



class SaleOrderTranslation extends MainModel
{
    protected $table = 'sales_order_translation';
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
		"created_date",
		"updated_by_id",
		"updated_date",
		"is_backup",
		"lang_code",
		"sales_order_id",
		"short_desc"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer"
    ];
}
