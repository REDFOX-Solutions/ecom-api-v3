<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ExternalApiKey extends MainModel
{
    protected $table = 'external_api_key';
   
    protected $primaryKey = 'id'; //table primary key column name, change it if it has different name
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increase

    protected $with = [];//The relations to eager load on every query.
    protected $withCount = [];//The relationship counts that should be eager loaded on every query.

    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    protected $fillable = [
        "id", 
        "created_by_id", 
        "updated_by_id", 
        "created_date", 
        "updated_date", 
        "is_backup", 
        "name", 
        "api_key", 
        "is_active",
        "company_id"
    ];//allow which fill to add into DB

    //The attributes that should be cast to native types.
    protected $casts = [
        "is_backup" => "integer",
        "is_active" => "integer"
    ];

    // To autp create children records 
    

    /** relation table */

    //formula fields
    protected $appends = [];
}
