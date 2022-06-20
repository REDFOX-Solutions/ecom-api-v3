<?php

namespace App\Model;



class Pricebook extends MainModel
{
    protected $table = 'pricebook';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
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
        "is_active", 
        "name",
        "is_standard",
        "for_pos"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "is_active" => "integer",
        "is_standard" => "integer",
        "for_pos" => "integer"
    ];

    // protected $appends = [''];
 
  

    public function pricebookEntries(){
        return $this->hasMany('App\Model\PricebookEntry', 'pricebook_id', 'id')->with("product");
    }

    public function createdBy() {
        return $this->belongsTo('App\Model\User', 'created_by_id');
    }
}
