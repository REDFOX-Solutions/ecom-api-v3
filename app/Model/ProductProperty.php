<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductProperty extends MainModel
{
    protected $table = 'product_properties';
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
        "products_id",
        "properties_id",
        "code",
        "is_active",
        "not_delete",
        "ordering"
    ]; 

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer",
        "not_delete" => "integer",
        "ordering" => "integer"
    ];

     
    public function property(){
        return $this->belongsTo('App\Model\Properties', 'properties_id');
    }

    public function product(){
        return $this->belongsTo('App\Model\Products', 'products_id');
    }

    public function propertyR(){
        return $this->belongsTo('App\Model\Properties', 'properties_id');
    }

    public function productR(){
        return $this->belongsTo('App\Model\Products', 'products_id');
    }

    public function langs(){
        return $this->hasMany('App\Model\ProductPropertyTranslation', 'product_properties_id', 'id');
    }
}
