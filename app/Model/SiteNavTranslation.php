<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SiteNavTranslation extends MainModel
{
    protected $table = 'site_nav_translation';
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
      "site_nav_id",
      "name"
    ];

    protected $casts = [
		"id" => "string",  
		"is_backup" => "integer", 
    ];
}
