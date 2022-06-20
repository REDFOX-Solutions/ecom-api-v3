<?php

namespace App\Model;



class Permissions extends MainModel
{
    protected $table = 'permissions';
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
        "name",
        "not_editable"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "not_editable" => "integer"
    ];

    public function objectPermissions(){
        return $this->hasMany('App\Model\ObjectPermission', "permission_id", "id")->with("object");
    }

    public function adminNavPermissions(){
        return $this->hasMany('App\Model\AdminNavPermission', "permission_id", "id") 
                    ->with("adminNav");
    }
}
