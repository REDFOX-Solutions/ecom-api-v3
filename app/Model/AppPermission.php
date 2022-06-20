<?php

namespace App\Model;



class AppPermission extends MainModel
{
    protected $table = 'app_permission';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id", "created_date", "created_by_id", "updated_date", "updated_by_id", "is_backup", 
        "sys_id", "app_id", "permission_id", "menu_ids", "visible"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "sys_id" => "integer",
        "visible" => "integer"
    ];

    public function application(){
        return $this->belongsTo("App\Model\Applications", "app_id");
    }

    public function permission(){
        return $this->belongsTo("App\Model\Permissions", "permission_id");
    }
}
