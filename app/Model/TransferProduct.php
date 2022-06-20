<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TransferProduct extends MainModel
{
    protected $table = 'transfer_product';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ["langs"];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date'; 

    protected $fillable = [
        "id"                , 
        "created_date"      , 
        "created_by_id"     , 
        "updated_date"      , 
        "updated_by_id"     ,
        "is_backup"         ,
        "ref_code"          , 
        "external_ref_code" , 
        "transfer_date"     ,
        "post_period"       ,
        "reason_code_id"    ,
        "total_qty"         ,
        "total_cost"        ,
        "transfer_type"     ,
        "from_warehouse_id" ,
        "to_warehouse_id"   
    ];

    protected $casts = [
		"id" => "string"        , 
        "is_backup" => "integer",
        "total_qty" => "double" ,
        "total_cost" => "double",
    ];

    protected $appends = [];

    public function langs(){
        return $this->hasMany('App\Model\TransferProdTranslate', 'transfer_product_id', 'id');
    }

    public function details(){
        return $this->hasMany('App\Model\TransferProductDetail', 'transfer_product_id', 'id');
    }
     

}
