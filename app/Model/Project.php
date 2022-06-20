<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Project extends MainModel
{
    protected $table = 'project';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs'];
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
        "start_date",
        "end_date",
        "status",
        "slug",
        "code",
        "category_ids",
        "is_active",
        "related_product_ids",
        "thumbnail"
    ];
    

    protected $casts = [
		"id" => "string",
        "is_backup" => "integer",
        "is_active" => "integer"
    ];

    // To autp create children records
    public static $relationship = [
        "children" => [ 
            ["name" => "project_properties", "parent_field" => "project_id", "controller" => "App\Http\Controllers\API\ProjectPropertyController"],
            ["name" => "photos", "parent_field" => "parent_id", "controller" => "App\Http\Controllers\API\PhotoController"]
        ]
    ];
    
    public function langs(){
        return $this->hasMany('App\Model\ProjectTranslation', 'project_id', 'id');
    }

    public function projectProperties(){
        return $this->hasMany('App\Model\ProjectProperties', 'project_id', "id")->with(['property']);
    }
    public function photos(){
        return $this->hasMany('App\Model\Photos', 'parent_id', 'id');
    }
    public function categoryR(){
        return $this->belongsTo('App\Model\Categories', 'category_ids');
    }
}
