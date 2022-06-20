<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Properties extends MainModel
{
    protected $table = 'properties';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs'];
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
        "code",
        "is_active",
        "is_multi",
        "recordtype_id",
        "recordtype_name",
        "ordering",
        "category_id",
        "icon",
        "image"
    ]; 

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "is_multi" => "integer",
        "is_active" => "integer",
        "ordering" => "integer"
    ];

    protected $appends = []; 
     
    public function products(){
        return $this->hasMany('App\Model\ProductProperty', 'properties_id', 'id')->with(["product"]);
    }
    public function langs(){
        return $this->hasMany('App\Model\PropertyTranslation', 'properties_id', 'id');
    }
}
