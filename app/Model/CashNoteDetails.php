<?php

namespace App\Model;



class CashNoteDetails extends MainModel
{
    protected $table = 'cash_note_details';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['currencySheet'];
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
        "currency_sheets_id",
        "amount",  
        "cash_note_id",
        "num_of_sheets"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "amount" => "double",
        "num_of_sheets" => "integer"
    ];

    protected $appends = [];

    public function cashNote(){
        return $this->belongsTo('App\Model\CashNote', 'cash_note_id');
    }

    public function currencySheet(){
        return $this->belongsTo('App\Model\CurrencySheets', 'currency_sheets_id');
    }

    
}
