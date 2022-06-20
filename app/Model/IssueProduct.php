<?php

namespace App\Model;
 

class IssueProduct extends MainModel
{
    protected $table = 'issue_product';
    protected $keyType = 'string';
    protected $with = ['langs'];
    protected $withCount = [];
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = 
    [
        "id", 
        "created_by_id", 
        "created_date", 
        "updated_by_id", 
        "updated_date", 
        "is_backup", 
        "ref_code", 
        "external_ref_code", 
        "issue_date", 
        "status", 
        "post_period", 
        "reason_code_id", 
        "total_qty", 
        "total_cost", 
        "warehouse_id", 
        "source"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer"
    ];

    public static $relationship = [
        "children" => [ 
            [
                "name" => "details", 
                "parent_field" => "issue_prod_id",
                "controller" => "App\Http\Controllers\API\IssueProductDetailController"
            ],
           
            
        ],
        "parent" => []
    ];

    /** relational table */
    public function langs(){
        return $this->hasMany('App\Model\IssueProductTranslate', 'issue_prod_id', 'id');
    }

    public function details(){
        return $this->hasMany('App\Model\IssueProductDetail', 'issue_prod_id', 'id')->with("product");
    }

    public function reasonCode(){
        return $this->belongsTo('App\Model\ReasonCode', "reason_code_id");
    }
     
}
