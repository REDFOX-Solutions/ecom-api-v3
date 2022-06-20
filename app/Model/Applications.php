<?php

namespace App\Model;



class Applications extends MainModel
{
    protected $table = 'applications';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ["langs"];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id", "created_date", "created_by_id", "updated_date", "updated_by_id", 
        "is_backup", "sys_id", "name", "active", "icon", "is_default", "ordering", "company_id", "category"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        
        "active" => "integer",
        "is_default" => "integer",
        "ordering" => "integer"
    ];

    // public function appPermissions(){
    //     return $this->hasMany("App\Model\AppPermission", "app_id", "id");
    // }

    public function menus(){
        return $this->hasMany("App\Model\AppMenus", "applications_id", "id")
                    ->where("parent_menu_id", null)
                    ->with("submenu")
                    ->orderBy('ordering', 'asc');
    }

    public function modules(){
        return $this->hasMany("App\Model\AppModules", "applications_id", "id");
    }

    public function langs(){
        return $this->hasMany('App\Model\ApplicationTranslation', 'applications_id', 'id');
    }
    public function company(){
        return $this->belongsTo('App\Model\Company','company_id');
    }
}