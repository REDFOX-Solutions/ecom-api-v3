<?php

namespace App\Model;



class CurrencySheets extends MainModel
{
    protected $table = 'currency_sheets';
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
        "label",  
        "ordering",
        "shop_currency_id",
        "image",
        "value"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "ordering" => "integer", 
        "value" => "double"
    ];

    protected $appends = [];

    public function shopCurrency(){
        return $this->belongsTo('App\Model\ShopCurrency', 'shop_currency_id');
    }
}
