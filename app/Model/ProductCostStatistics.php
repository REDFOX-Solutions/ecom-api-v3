<?php

namespace App\Model;



class ProductCostStatistics extends MainModel
{
    protected $table = 'product_coststatistics';
    public $incrementing = false;//set this to false if your primary key isn't auto increase
    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    protected $fillable=[
    "id",
    "created_by_id",
    "updated_by_id",
    "created_date",
    "updated_date",
    "is_backup",
    "last_cost",
    "average_cost",
    "min_cost",
    "max_cost",
    "products_id"
    ];

    protected $casts = [
		"id" => "string", 
        "last_cost" => "double", 
        "average_cost" => "integer",
        "min_cost" => "double",
        "max_cost" => "double",
    ];

    public function productCostStatistics(){
        return $this->belongsTo('App\Model\Products', 'id');
    }
}
