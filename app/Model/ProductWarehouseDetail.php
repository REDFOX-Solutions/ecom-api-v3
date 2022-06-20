<?php

namespace App\Model;



class ProductWarehouseDetail extends MainModel
{
    protected $table = 'product_warehouse_details';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = 
    [
        'id'                       ,   
        'created_date'             ,   
        'updated_date'             ,   
        'created_by_id'            ,   
        'updated_by_id'            ,   
        'is_backup'                ,   
        'code'                     ,   
        'is_active'                ,   
        'note'                     ,   
        'products_id'              ,    
        'trans_type'               ,   
        'warehouses_id'            ,    
        'location_id'              ,   
        'uom'                      ,
        'is_default'               ,
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer",
        "is_default"=> "integer",
    ];


    /** relational table */

    /** (many to one)*/
    public function product()
    {
        return $this->belongsTo(
            'App\Model\Products',    //Parent model
            'products_id'
        );
    }


    public function warehouse()
    {

        return $this->belongsTo(
            'App\Model\Warehouse',   //Parent model
            'warehouses_id'
        );
        
    }


    public function warehouse_location()
    {
        return $this->belongsTo(
            'App\Model\WarehouseLocation',   //Parent model
            'location_id'
        );
    }



}

