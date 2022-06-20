<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InvAdjDetail extends MainModel
{
    protected $table = 'inv_adj_detail';
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
        "qty"                   ,
        "location_id"           ,
        "warehouse_id"          ,
        "unit_cost"             ,
        "inv_adj_id"    
    ];

    protected $casts = [
		"id" => "string"            , 
        "is_backup" => "integer"    ,
        "qty" => "double"
    ];

    protected $appends = [];
    
    public function langs(){
        return $this->hasMany('App\Model\InvAdjDetailTranslate', 'inv_adj_detail_id', 'id');
    }
}
