<?php

namespace App\Model;



class TagTranslation extends MainModel
{
    protected $table = 'tag_translation';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id", "created_date", "created_by_id", 
        "updated_date", "updated_by_id", "is_backup", 
        "lang_code", "tags_id", "name", "short_desc"
    ];
    protected $casts = [
		"id" => "string",  
		"is_backup" => "integer", 
    ];
}
