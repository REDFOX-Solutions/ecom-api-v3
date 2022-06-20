<?php

namespace App\Model;



class TheoryCollection extends MainModel
{
    protected $table = 'theory_collection';
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
        "payment_method_id", 
        "amount", 
        "staff_shifts_id",
        "transaction_date"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "amount" => "double"
    ];

    protected $appends = [];

    public function staffShift(){
        return $this->belongsTo('App\Model\StaffShifts', 'staff_shifts_id');
    }
    public function paymentMethod(){
        return $this->belongsTo('App\Model\PaymentMethod', 'payment_method_id');
    }
    
}
