<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TransferProductDetail extends MainModel
{
    protected $table = 'transfer_product_detail';
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
        "transfer_product_id"   , 
        "product_id"            , 
        "trans_uom_id"          ,
        "base_uom_id"           ,
        "reason_code_id"        ,
        "from_location_id"      ,
        "to_location_id"
    ];

    protected $casts = [
		"id" => "string"        , 
        "is_backup" => "integer",
    ];

    protected $appends = [];
    
    public function langs(){
        return $this->hasMany('App\Model\TransferProdDetailTranslate', 'transf_prod_detail_id', 'id');
    }
}
