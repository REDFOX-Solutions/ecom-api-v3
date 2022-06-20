<?php

namespace App\Model;



class ObjectPermission extends MainModel
{
    protected $table = 'object_permission';
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
        "object_id", 
        "view", 
        "create", 
        "delete", 
        "edit", 
        "permission_id",
        "view_all"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "view" => "integer", 
        "create" => "integer", 
        "delete" => "integer", 
        "edit" => "integer", 
        "view_all" => "integer"
    ];

    public function object(){
        return $this->belongsTo('App\Model\Objects', 'object_id');
    }

    public function permission(){
        return $this->belongsTo('App\Model\Permissions', 'permission_id');
    }
}
