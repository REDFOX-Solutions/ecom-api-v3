<?php

namespace App\Model;



class KitSpecDetail extends MainModel
{
    protected $table = 'kit_spec_details';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable =
    [
        'id'                   ,               
        'created_date'         ,
        'updated_date'         ,
        'created_by_id'        ,
        'updated_by_id'        ,
        'is_backup'            ,
        'is_active'            ,
        'description'          ,
        'note'                 ,
        'component_qty'        ,
        'min_qty'              ,
        'max_qty'              ,
        'unit'                 ,
        'component_id'         ,
        'kit_specification_id' ,
        'allow_variance'
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer",
        "min_qty" => "double",
        "max_qty" => "double",
        "allow_variance" => "integer",
        "component_qty" => "double"
    ];
    /** relational table */

    /** (many to one)*/
    public function kit_specification()
    {
        return $this->belongsTo(
            'App\Model\KitSpecification',    //Parent model
            'kit_specification_id'
        );
    }

    /** (many to one)*/
    public function product()
    {
        return $this->belongsTo(
            'App\Model\Products',    //Parent model
            'component_id'
        );
    }
}
