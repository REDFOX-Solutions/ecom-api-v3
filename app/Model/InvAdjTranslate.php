<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InvAdjTranslate extends MainModel
{
    protected $table = 'inv_adj_translate';
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
        "inv_adj_id"            , 
        "description"      
    ];

    protected $casts = [
		"id" => "string"        , 
        "is_backup" => "integer"
    ];
    
     
}
