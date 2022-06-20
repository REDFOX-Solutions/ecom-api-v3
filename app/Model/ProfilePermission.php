<?php

namespace App\Model;



class ProfilePermission extends MainModel
{
    protected $table = 'profile_permission';
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
        "object_name", 
        "record_id", //it is dynamic depand on object name; DONT CREATE RELATIONSHIP
        "profiles_id", 
        "is_visible"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_visible" => "integer"
    ];

    protected $appends = [];

    public function profile(){
        return $this->belongsTo('App\Model\Profile', 'profiles_id', 'id');
    }

    public function application(){
        return $this->belongsTo('App\Model\Applications', 'record_id', 'id');
    }

    public function appMenu(){
        return $this->belongsTo('App\Model\AppMenus', 'record_id', 'id');
    }

    public function appModule(){
        return $this->belongsTo('App\Model\AppModules', 'record_id', 'id');
                    
    }
}
