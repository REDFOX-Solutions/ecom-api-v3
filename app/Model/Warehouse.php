<?php

namespace App\Model;



class Warehouse extends MainModel
{
    protected $table = 'warehouses';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';



    protected $fillable = 
    [
        'id'                  ,       
        'created_date'        ,
        'updated_date'        ,
        'created_by_id'       ,
        'updated_by_id'       ,
        'is_backup'           ,
        'code'                ,
        'is_active'           ,
        'desc'                ,
        'name'                ,
        'freeze_inventory'    ,
        'note'                
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer",
        "freeze_inventory" => "integer"
    ];

    /** relational table */
 
    /** (one to many)*/
    public function locations()
    {
        return $this->hasMany(
            'App\Model\WarehouseLocation',  //child model
            'warehouses_id',                //fk in child model
            'id'                            //local pk
        );
    }

    public function contacts()
    {
        return $this->hasMany(
            'App\Model\Contact', //child model
            'parent_id',         //fk in child model
            'id'                 //local pk
        );
    }



}
