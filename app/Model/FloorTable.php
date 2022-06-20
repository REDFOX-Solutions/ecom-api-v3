<?php

namespace App\Model;



class FloorTable extends MainModel
{
    protected $table = 'floor_table';
    
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increase

    protected $with = [];//The relations to eager load on every query.
    protected $withCount = [];//The relationship counts that should be eager loaded on every query.

    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    protected $fillable = [
        //System fields
        "id", 
        "created_by_id", 
        "updated_by_id", 
        "created_date", 
        "updated_date", 
        "is_backup", 

        //manual input fields
        "floor_level", 
        "table_name", 
        "status"//active, busy, reserved
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "floor_level" => "integer"
    ];

    protected $appends =[];

    public function activeOrders(){
        return $this->hasMany('App\Model\SaleOrder', 'floor_table_id', 'id')
                    ->where("status", "hold");
    } 

    
}
