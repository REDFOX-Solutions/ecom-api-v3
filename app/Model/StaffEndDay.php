<?php

namespace App\Model;



class StaffEndDay extends MainModel
{
    protected $table = 'staff_end_day';
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
        "end_date", 
        "end_by_id", 
        "approved_by_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
    ];

    protected $appends = [];

    public function staffShift(){
        return $this->hasMany('App\Model\StaffShifts', 'staff_end_day_id', "id");
    }
}
