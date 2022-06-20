<?php

namespace App\Model;



class Tag extends MainModel
{
    protected $table = 'tags';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs'];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id", "created_date", "created_by_id", 
        "updated_date", "updated_by_id", "is_backup", "type",
        "slug" 
    ];
    protected $casts = [
		"id" => "string",  
        "is_backup" => "integer",
        "type" => "integer"
    ];
    // protected $appends = ["total_product"]; 

    // public function getTotalProductAttribute(){ 
    //     $count = Products::where('tag_ids', '<>', null)->where('tag_ids', 'like', '%'. $this->id .'%')->count();
    //     return $count; 
    // }
 
    public function langs(){
        return $this->hasMany('App\Model\TagTranslation', 'tags_id', 'id');
    }
}
