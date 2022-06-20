<?php

namespace App\Model;



class KitSpecification extends MainModel
{
    protected $table = 'kit_specification';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = 
    [
        'id'            ,   
        'created_date'  ,
        'updated_date'  ,
        'created_by_id' ,
        'updated_by_id' ,
        'is_backup'     ,
        'is_active'     ,
        'desc'          ,
        'note'          ,
        'products_id'   ,
        'revision'      ,
        'allow_component_addition'      ,
        'uom_id'           ,
        'qty'           , 
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer",
        "qty" => "double",
        "allow_component_addition" => "integer",
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

    /** (many to one)*/
    public function kit_spec_details()
    {
        return $this->hasMany(
            'App\Model\KitSpecDetail',                //child model
            'kit_specification_id',                //fk in child model
            'id'                                   //local pk
        )->with('product');
    }


}
