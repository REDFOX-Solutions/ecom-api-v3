<?php

namespace App\Model;



class ProductOptionKey extends MainModel
{
    protected $table = 'options_key';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable =
    [
        'id',
        'created_date',
        'updated_date',
        'created_by_id',
        'updated_by_id',
        'is_backup',
        'products_id',
        'desc',
        'note',
        'code',
        'values'          
    ];


    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer"
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
