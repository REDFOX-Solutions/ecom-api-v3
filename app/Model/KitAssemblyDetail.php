<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class KitAssemblyDetail extends MainModel
{
    protected $table = 'kit_assembly_detail';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
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
        "kit_assembly_id"   , 
        "product_id"        , 
        "trans_uom_id"      ,
        "base_uom_id"       ,
        "qty"               ,
        "location_id"       ,
        "unit_cost"         ,
        "variant_qty"       ,
        "product_type"      
    ];

    protected $casts = [
		"id" => "string"            , 
        "is_backup" => "integer"    ,
        "qty" => "double"           ,
        "unit_cost" => "double"     ,
        "variant_qty" => "double"   
    ];

    protected $appends = [];
}
