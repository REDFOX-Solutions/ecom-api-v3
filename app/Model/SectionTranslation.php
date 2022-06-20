<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SectionTranslation extends MainModel
{
    protected $table = 'sections_translation';
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
		"title",
		"subtitle",
		"short_desc",
		"sections_id",
		"contents"
	];
	
	protected $casts = [
		"id" => "string", 
        'sections_id' => 'string',
		"is_backup" => "integer", 
    ];
}
