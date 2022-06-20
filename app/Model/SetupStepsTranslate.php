<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SetupStepsTranslate extends MainModel
{
    protected $table = 'setup_step_translate';  
    protected $with = [];
    protected $withCount = []; 
    
    protected $fillable = [
        "id", 
		"created_date",
		"created_by_id",
		"updated_date",
        "updated_by_id", 
        "is_backup",
        "lang_code",
        "label", 
        "description",
        "setup_steps_id"
    ];

    protected $casts = [
		"id" => "string",  
        "is_backup" => "integer"
    ];
	
	protected $appends = []; 
	protected $hidden = [];
}
