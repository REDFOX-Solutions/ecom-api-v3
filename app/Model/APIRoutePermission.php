<?php

namespace App\Model;

class APIRoutePermission extends MainModel
{
    protected $table = 'api_route_permission';
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
        "is_get",
        "is_post",
        "is_put",
        "is_delete",
        "profiles_id",
        "api_routes_id"
    ];

    protected $casts = [
		"id" => "string",  
        "is_backup" => "integer",
        "is_get" => "integer",
        "is_post" => "integer",
        "is_put" => "integer",
        "is_delete" => "integer"
    ];
	
	protected $appends = []; 
    protected $hidden = [];
    
    public function profile(){
        return $this->belongsTo('App\Model\Profile', 'profiles_id');
    }

    public function apiRoute(){
        return $this->belongsTo('App\Model\APIRoute', 'api_routes_id');
    }
}
