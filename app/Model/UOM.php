<?php

namespace App\Model;



class UOM extends MainModel
{
    protected $table = 'uom_master';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = 
    [
        'id'             ,
        'created_by_id'  ,
        'updated_by_id'  ,
        'created_date'   ,
        'updated_date'   ,
        'is_backup'      ,
        'is_active'      ,
        'uom'            ,
        'code'           ,
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer"
    ];

}
