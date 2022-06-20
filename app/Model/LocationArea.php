<?php

namespace App\Model;



class LocationArea extends MainModel
{
    protected $table = 'location_area';
    protected $keyType = 'string';
    public $incrementing = false; 
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id", 
        "created_by_id", 
        "updated_by_id", 
        "created_date", 
        "updated_date", 
        "is_backup", 
        "name", 
        "parent_id", 
        "area_type", //country, district, commune
        "lat", 
        "log", 
        "post_code"
	];
	
	protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "lat" => "double", 
        "log" => "double" 
    ];
 
}
