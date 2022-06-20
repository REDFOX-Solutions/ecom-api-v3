<?php

namespace App\Model;



class AvailableLanguages extends MainModel
{
    protected $table = 'available_languages';
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
        "is_defualt",
        "lang_code",
        "company_id",
        "name",
        "icon",
        "image_flag",
        "short_name"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_defualt" => "integer"
    ];
}
