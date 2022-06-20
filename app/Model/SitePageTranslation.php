<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SitePageTranslation extends MainModel
{
    protected $table = 'site_page_translation';
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
      "site_pages_id",
      "title",
      "short_desc"
    ];

    protected $casts = [
		"id" => "string",  
		"is_backup" => "integer", 
    ];
}
