<?php

namespace App\Model;



class AppModules extends MainModel
{
    protected $table = 'app_modules';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        "id", "created_date", "created_by_id", "updated_date", "updated_by_id", 
        "is_backup", "name", "applications_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer"
    ];

    public function application(){
        return $this->belongsTo('App\Model\Applications', "applications_id");
    }
}
