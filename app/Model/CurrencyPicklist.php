<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CurrencyPicklist extends MainModel
{
    protected $table = 'currency_picklist';  
    protected $with = [];
    protected $withCount = []; 
    
    protected $fillable = [
        "id", 
		"created_date",
		"created_by_id",
		"updated_date",
        "updated_by_id", 
        "is_backup",
        "name", 
        "symbol", 
        "icon", 
        "code"
    ];

    protected $casts = [
		"id" => "string",  
        "is_backup" => "integer"
    ];
	
	protected $appends = []; 
	protected $hidden = [];
}
