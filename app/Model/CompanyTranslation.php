<?php

namespace App\Model;



class CompanyTranslation extends MainModel
{
    protected $table = 'company_translate';
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
		"address_line",
		"billing_address",
		"name",
		"general_director",
		"company_id",
		"about",
		"keywords",
		"vision",
		"mission"
	];
	
	protected $casts = [
		"id" => "string", 
		"is_backup" => "integer",
		"company_id" => "string"
    ];
}
