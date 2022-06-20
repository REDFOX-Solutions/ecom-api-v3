<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PhysicalCount extends MainModel
{
    protected $table = 'physical_count';
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
        "phycount_date"     ,
        "status"            ,
        "post_period"       ,
        "reason_code_id"    ,
        "total_qty"         ,
        "total_cost"        ,
        "warehouse_id"      ,
        "sheet"   
    ];

    protected $casts = [
		"id" => "string"        , 
        "is_backup" => "integer",
        "total_qty" => "double" ,
        "total_cost" => "double",
    ];

    protected $appends = [];

    public function langs(){
        return $this->hasMany('App\Model\PhysicalCountTranslate', 'physical_count_id', 'id');
    }

    public function details(){
        return $this->hasMany('App\Model\PhysicalCountDetail', 'physical_count_id', 'id');
    }
}
