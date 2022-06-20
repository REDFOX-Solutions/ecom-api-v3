<?php

namespace App\Model;



class WarehouseLocation extends MainModel
{
    protected $table = 'warehouse_locations';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = 
    [
        'id'              ,
        'created_date'    ,
        'updated_date'    ,
        'created_by_id'   ,
        'updated_by_id'   ,
        'is_backup'       ,
        'code'            ,
        'is_active'       ,
        'desc'            ,
        'note'            ,
        'trans_types'     ,
        'warehouses_id'         
    ];


    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer"
    ];

    /** relational table */

    /** (many to one)*/
    public function warehouse()
    {
        return $this->belongsTo(
            'App\Model\Products',    //Parent model
            'warehouses_id'
        );
    }
}
