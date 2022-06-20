<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SetupSteps extends MainModel
{
    protected $table = 'setup_steps';  
    protected $with = ['langs'];
    protected $withCount = []; 
    
    protected $fillable = [
        "id", 
		"created_date",
		"created_by_id",
		"updated_date",
        "updated_by_id", 
        "is_backup",
        "name", 
        "ordering", 
        "is_config"
    ];

    protected $casts = [
		"id" => "string",  
        "is_backup" => "integer",
        "ordering" => "integer",
        "is_config" => "integer"
    ];
	
	protected $appends = []; 
	protected $hidden = [];

    public function langs(){
        return $this->hasMany('App\Model\SetupStepsTranslate', 'setup_steps_id', 'id');
    }
}
