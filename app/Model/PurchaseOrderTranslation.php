<?php

namespace App\Model;



class PurchaseOrderTranslation extends MainModel
{
    protected $table = 'po_translation';
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
		"description", 
		"purchase_orders_id", 
		"lang_code"
	];
	
	protected $casts = [
		"id" => "string", 
		"is_backup" => "integer"
	];

	// protected $appends = ['fullname'];
    public function getFullnameAttribute(){
         
        return "{$this->description}";
    }
}
