<?php

namespace App\Model;



class PersonAccountTranslation extends MainModel
{
    protected $table = 'person_account_translation';
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
		"lastname", 
		"firstname", 
		"nickname", 
		"address_line", 
		"billing_address_line", 
		"lang_code", 
		"short_description", 
		"person_account_id", 
		"birth_address", 
		"note", 
		"occupation",
		"experience",
		"knowledge",
		"culture_lvl",
		"training_lvl"
	];
	
	protected $casts = [
		"id" => "string", 
		"is_backup" => "integer"
	];

	protected $appends = ['fullname'];
    public function getFullnameAttribute(){
         
        return "{$this->firstname} {$this->lastname}";
    }
}
