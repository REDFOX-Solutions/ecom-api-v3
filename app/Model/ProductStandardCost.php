<?php

namespace App\Model;

use App\Services\GlobalStaticValue;


class ProductStandardCost extends MainModel
{
    protected $table = 'product_standard_cost';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
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
        "current_cost",
        "effective_date",
        "products_id",
        "is_active"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "current_cost" => "double",
        "is_active" => "integer"
    ];

    protected $appends = [];

    public function product(){
        return $this->belongsTo('App\Model\Products', 'products_id');
    }

}
