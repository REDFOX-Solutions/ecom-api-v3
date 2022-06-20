<?php

namespace App\Model;



class Profile extends MainModel
{
    protected $table = 'profiles';
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
        "is_custom",
        "company_id",
        "system_admin"
    ];

    protected $casts = [
		"id" => "string",  
        "is_backup" => "integer",
        "is_active" => "integer",
        "is_custom" => "integer",
        "system_admin" => "integer"
    ];
	
	protected $appends = []; 
	protected $hidden = [];

    public function applications(){
        return $this->hasMany('App\Model\ProfilePermission', 'profiles_id')->where("object_name", 'applications')->with("application");
    }

    public function appMenus(){
        return $this->hasMany('App\Model\ProfilePermission', 'profiles_id')->where("object_name", 'app_menus')->with("appMenu");
    }

    public function appModules(){
        return $this->hasMany('App\Model\ProfilePermission', 'profiles_id')
                    ->where("object_name", 'app_modules')
                    ->with("appModule");
    }
    public function users(){
        return $this->hasMany('App\Model\User', 'profile_ids','id');
    }
}
