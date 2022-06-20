<?php

namespace App\Model;



class PurchasePaymentTranslation extends MainModel
{
    protected $table = 'purchase_payment_translation';
    protected $keyType = 'string';
    public $incrementing = false;
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
        "lang_code",
		"description", 
		"purchase_payment_id"
	];
	
	protected $casts = [
		"id" => "string", 
		"is_backup" => "integer"
	];

	// protected $appends = ['fullname'];
    // public function getFullnameAttribute(){
         
    //     return "{$this->description}";
    // }
}
