<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CompanyFacilities extends MainModel
{
    protected $table = 'company_facilites';
   
    protected $primaryKey = 'id'; //table primary key column name, change it if it has different name
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increase

    protected $with = [];//The relations to eager load on every query.
    protected $withCount = [];//The relationship counts that should be eager loaded on every query.

    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    //allow which fill to add into DB
    protected $fillable = [
        "id", 
        "created_by_id", 
        "updated_by_id", 
        "created_date", 
        "updated_date", 
        "is_backup", 
        "company_id",
        "facility_id",
        "charge_status"
    ];

    //The attributes that should be cast to native types.
    protected $casts = [
        "is_backup" => "integer",
    ];

    /**
     * Get the company that owns the CompanyFacilities
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facilities::class, 'facility_id');
    }
}
