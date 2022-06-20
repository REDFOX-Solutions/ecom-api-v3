<?php

namespace App\Model;



class CostHistory extends MainModel
{
    protected $table = 'cost_histories';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = 
    [
        
        'id'           ,
        'created_by_id',
        'updated_by_id',
        'created_date' ,
        'updated_date' ,
        'is_backup'    ,
        'is_active'    ,
        'products_id'  ,
        'cost'         
    ];


    /** relational table */
    
    /** (many to one)*/
    public function product()
    {
        return $this->belongsTo(
            'App\Model\Products',    //Parent model
            'products_id',          //fk in local model 
            'id'                    //pk in parent model
        );
    }

}
