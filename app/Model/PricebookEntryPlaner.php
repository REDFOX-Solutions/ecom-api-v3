<?php

namespace App\Model;
 

class PricebookEntryPlaner extends MainModel
{
    protected $table = 'pbe_planer';
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
        "start_date", 
        "end_date", 
        "rate_type", //percent, currency
        "rate", 
        "name", 
        "comments", 
        "is_increase", 
        "pbe_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "rate" => "double",
        "is_increase" => "integer"
    ];

    protected $appends = [];
 
    public function pricebookEntryR(){
        return $this->belongsTo(PricebookEntry::class, 'pbe_id');
    }
}
