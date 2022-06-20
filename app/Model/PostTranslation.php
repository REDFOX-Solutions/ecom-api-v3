<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PostTranslation extends MainModel
{
    protected $table = 'post_translation';
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
		"short_desc",
		"posts_id",
		"contents",  
		"meta_title", 
		"meta_desc", 
		"meta_keyword",
		"target_link",
		"description",
		"note",
		"subtitle"
	];
	
	protected $casts = [
		"id" => "string",
		'posts_id' => 'string',
		"is_backup" => "integer", 
    ];
}
