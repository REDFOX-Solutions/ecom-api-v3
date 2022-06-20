<?php

namespace App\Model;
 
class IssueProductDetailTranslate extends MainModel
{
    protected $table = 'issue_prod_detail_translate';
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
		"issue_prod_detail_id"
	];
	
	protected $casts = [
		"id" => "string", 
		"is_backup" => "integer"
    ]; 
}
