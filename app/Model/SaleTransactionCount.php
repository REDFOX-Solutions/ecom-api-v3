<?php

namespace App\Model;



class SaleTransactionCount extends MainModel
{
    protected $table = 'sale_transaction_count';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date'; 

    protected $fillable = [
        "id", 
        "created_date", 
        "created_by_id", 
        "updated_date", 
        "updated_by_id",
        "is_backup", 
        "order_type", 
        "qty", 
        "total_amount", 
        "staff_shifts_id",
        "transaction_date",
        "channel"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "total_amount" => "double",
        "qty" => "integer"
    ];

    protected $appends = [];

    public function staffShift(){
        return $this->belongsTo('App\Model\StaffShifts', 'staff_shifts_id');
    }
}
