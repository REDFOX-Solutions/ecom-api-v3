<?php

namespace App\Model;



class PaymentMethod extends MainModel
{
    protected $table = 'payment_methods';
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
        "payment_type", //cash, card, cheque
        "payment_name", 
        "image_url", 
        "comments",
        "is_default",
        "is_active", 
        "ordering"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_default" => "integer",
        "is_active" => "integer",
        "pos_usable" => "integer",
        "ordering" => "integer"
    ];

    protected $appends = [];  
 
}
