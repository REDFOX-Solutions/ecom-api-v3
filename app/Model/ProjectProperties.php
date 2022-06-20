<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProjectProperties extends MainModel
{
    protected $table = 'project_properties';
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
        "project_id",
        "property_id"
    ];
    

    protected $casts = [
		"id" => "string",
        "is_backup" => "integer"
    ];

    public function langs(){
        return $this->hasMany('App\Model\ProjPropTranslate', 'project_properties_id', 'id');
    }
    public function property(){
        return $this->belongsTo('App\Model\Properties', 'property_id');
    }
}