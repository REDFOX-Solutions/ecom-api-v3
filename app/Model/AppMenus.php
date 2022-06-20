<?php

namespace App\Model;



class AppMenus extends MainModel
{
    protected $table = 'app_menus';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date'; 

    protected $fillable = [
        "id", "created_date", "created_by_id", "updated_date", "updated_by_id", "is_backup",
        "title", "icon", "path", "class", "is_external_link", "is_divider", "parent_menu_id", "applications_id", "ordering", "is_setting"

    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        // "sys_id" => "integer",
        "ordering" => "integer",
        "is_external_link" => "integer",
        "is_divider" => "integer",
        "is_setting" => "integer"
    ];

    public function submenu(){
        return $this->hasMany('App\Model\AppMenus', "parent_menu_id", "id")->with("submenu")->orderBy('ordering', 'asc');
    }
}
