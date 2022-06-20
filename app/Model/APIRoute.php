<?php

namespace App\Model;

class APIRoute extends MainModel
{
    protected $table = 'api_routes';
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
        "description",
        "is_active",
        "url"
    ];

    protected $casts = [
		"id" => "string",  
        "is_backup" => "integer",
        "is_active" => "integer"
    ];
	
	protected $appends = []; 
	protected $hidden = [];
}
