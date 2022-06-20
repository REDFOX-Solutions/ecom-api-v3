<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InvAdjustment extends MainModel
{
    protected $table = 'inv_adjustment';
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
        "adj_date"     ,
        "status"            ,
        "post_period"       ,
        "reason_code_id"    ,
        "total_qty"         ,
        "total_cost"        ,
        "physical_count_id"  
    ];

    protected $casts = [
		"id" => "string"        , 
        "is_backup" => "integer",
        "total_qty" => "double" ,
        "total_cost" => "double",
    ];

    protected $appends = [];

    public function langs(){
        return $this->hasMany('App\Model\InvAdjTranslate', 'inv_adj_id', 'id');
    }

    public function details(){
        return $this->hasMany('App\Model\InvAdjDetail', 'inv_adj_id', 'id');
    }

    /**
     * Get the physicalCount that owns the InvAdjustment
     * 
     */
    public function physicalCount()
    {
        return $this->belongsTo(PhysicalCount::class, 'physical_count_id');
    }

    public function reasonCode()
    {
        return $this->belongsTo(ReasonCode::class, 'reason_code_id');
    }
    

}
