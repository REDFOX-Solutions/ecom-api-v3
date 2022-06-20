<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProjPropTranslate extends MainModel
{
    protected $table = 'proj_prop_translate';
    protected $keyType = 'string';
    public $incrementing = false;
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
        "project_properties_id",
        "value"
    ];

    protected $casts = [
		"id" => "string", 
		"is_backup" => "integer", 
        "lang_code" => "string"
    ];
}
