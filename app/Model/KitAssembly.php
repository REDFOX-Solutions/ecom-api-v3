<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class KitAssembly extends MainModel
{
    protected $table = 'kit_assembly';
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
        "ref_code"          , 
        "external_ref_code" , 
        "assembly_date"     ,
        "status"            ,
        "post_period"       ,
        "reason_code_id"    ,
        "total_qty"         ,
        "total_cost"        ,
        "product_id"        ,
        "assembly_type"     , 
        "post_date"         ,
        "warehouse_id"      , 
        "kit_spec_id"       , 
        "revision"          ,   
        "trans_uom_id"      , 
        "base_uom_id"       ,  
        "location_id"       ,
        "qty"   
    ];

    protected $casts = [
		"id" => "string"        , 
        "is_backup" => "integer",
        "total_qty" => "double" ,
        "total_cost" => "double",
        "qty" => "double"
    ];

    protected $appends = [];

    public function details(){
        return $this->hasMany('App\Model\KitAssemblyDetail', 'kit_assembly_id', 'id');
    }
}
