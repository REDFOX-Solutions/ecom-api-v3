<?php

namespace App\Model;



class RecordType extends MainModel
{
    protected $table = 'record_type';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id", 
        "created_date", 
        "created_by_id", 
        "updated_date", 
        "updated_by_id",  
        "is_backup", 
        "object_name", 
        "name", 
        "label"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer"
    ];
 
}
