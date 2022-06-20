<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReasonCodeTranslation extends MainModel
{
    protected $table = 'reason_code_translation';
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
        "lang_code", 
        "description", 
        "reason_code_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
    ];
}
