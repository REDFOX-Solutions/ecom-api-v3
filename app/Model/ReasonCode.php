<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReasonCode extends MainModel
{
    protected $table = 'reason_code';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ["langs"];
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
        "reason_type", 
        "code",
        "coa_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer",
    ];

    protected $appends = [];

    public function langs(){
        return $this->hasMany('App\Model\ReasonCodeTranslation', 'reason_code_id', 'id');
    }
}
