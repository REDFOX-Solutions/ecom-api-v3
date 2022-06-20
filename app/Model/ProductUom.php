<?php

namespace App\Model;



class ProductUom extends MainModel
{
    protected $table = 'product_uoms';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = 
    [
        'id'                 ,       
        'created_date'       ,
        'updated_date'       ,
        'created_by_id'      ,
        'updated_by_id'      ,
        'is_backup'          ,
        'code'               ,
        'is_active'          ,
        'desc'               ,
        'note'               ,
        'products_id'        ,
        'to_unit'            ,
        'from_unit'          ,
        'conversion_method'  ,
        'conversion_factor'  
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


}
