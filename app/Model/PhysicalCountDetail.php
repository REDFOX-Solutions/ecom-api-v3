<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PhysicalCountDetail extends MainModel
{
    protected $table = 'physical_count_detail';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ["langs"];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date'; 

    protected $fillable = [
        "id"                    , 
        "created_date"          , 
        "created_by_id"         , 
        "updated_date"          , 
        "updated_by_id"         ,
        "is_backup"             ,
        "physical_count_id"     , 
        "product_id"            , 
        "trans_uom_id"          ,
        "base_uom_id"           ,
        "location_id"           ,
        "unit_cost"             ,
        "actual_qty"            ,
        "on_hand_qty"           ,
        "adj_qty"
    ];

    protected $casts = [
		"id" => "string"            , 
        "is_backup" => "integer"    ,
        "unit_cost" => "double"     ,
        "actual_qty" => "double"    ,
        "on_hand_qty" => "double"    ,
        "adj_qty" => "double"
    ];

    protected $appends = [];
    
    public function langs(){
        return $this->hasMany('App\Model\PhysicalCountDetailTranslate', 'phycount_detail_id', 'id');
    }
}
