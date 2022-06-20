<?php

namespace App\Model;
 

class Channel extends MainModel
{
    protected $table = 'channel';
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
        "name", 
        "website"
    ];

    protected $casts = [
        "is_backup" => "integer"
    ];

    protected $appends = [];
}
