<?php

namespace App\Model;



class SaleOrderPrepayment extends MainModel
{
    protected $table = 'saleorder_prepayment';
   
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
        "created_date", 
        "created_by_id", 
        "updated_date", 
        "updated_by_id", 
        "is_backup", 
        "sale_order_id", 
        "receipt_id", 
        "amount", 
        "status"
    ];//allow which fill to add into DB

    //The attributes that should be cast to native types.
    protected $casts = [
        "is_backup" => "integer",
        "amount" => "double"
    ];
    /** relation table */

    /** parent (Relationship many to one)*/
    public function saleorder(){
        return $this->belongsTo('App\Model\SaleOrder', "sale_order_id");
    }

    public function receipt(){
        return $this->belongsTo('App\Model\Receipts', "receipt_id");
        
    }
}
