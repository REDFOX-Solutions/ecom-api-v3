<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TransferProdDetailTranslate extends MainModel
{
    protected $table = 'transfer_prod_detail_translate';
    protected $keyType = 'string';
    public $incrementing = false;
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
        "lang_code"             , 
        "transf_prod_detail_id" , 
        "description"      
    ];

    protected $casts = [
		"id" => "string"        , 
        "is_backup" => "integer"
    ];
    
    
    public function getFullnameAttribute(){
         
        return "{$this->description}";
    }
}
