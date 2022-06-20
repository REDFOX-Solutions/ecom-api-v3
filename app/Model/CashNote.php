<?php

namespace App\Model;



class CashNote extends MainModel
{
    protected $table = 'cash_note';
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
        "amount",  
        "staff_shifts_id",
        "payment_method_id",
        "shop_currency_id",
        "transaction_date"
    ];
    //cash_note_details

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "amount" => "double", 
    ];

    protected $appends = [];

    public function staffShift(){
        return $this->belongsTo('App\Model\StaffShifts', 'staff_shifts_id');
    }

    public function shopCurrency(){
        return $this->belongsTo('App\Model\ShopCurrency', 'shop_currency_id');
    }

    public function cashNoteDetails(){
        return $this->hasMany('App\Model\CashNoteDetails', 'cash_note_id', 'id');
    }

    
}
