<?php

namespace App\Model;



class ShopCurrency extends MainModel
{
    protected $table = 'shop_currency';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['currency'];
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
        "is_base",  
        "developer_name",
        "rate_to_base",
        "ordering",
        "company_id",
        "rate_method", //div, mul
        "is_active",
        "pos_usable",
        "currency_picklist_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_base" => "integer",
        "rate_to_base" => "double",
        "ordering" => "integer", 
        "is_active" => "integer",
        "pos_usable" => "integer"
    ];

    protected $appends = [];

    public function currencySheets(){
        return $this->hasMany('App\Model\CurrencySheets', 'shop_currency_id', 'id');
    }

    public function exchangeRates(){
        return $this->hasMany('App\Model\CurrencyExchangeRate', 'from_currency_id', 'id');
    }
    
    public function currency(){
        return $this->belongsTo('App\Model\CurrencyPicklist', 'currency_picklist_id');
    }

}
