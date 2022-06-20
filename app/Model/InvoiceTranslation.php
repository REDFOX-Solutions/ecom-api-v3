<?php

namespace App\Model;



class InvoiceTranslation extends MainModel
{
    protected $table = 'invoice_translation';
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
		"comments",
		"invoices_id",
		"note"
	];
	
	protected $casts = [
		"id" => "string", 
		"is_backup" => "integer"
    ];
}
