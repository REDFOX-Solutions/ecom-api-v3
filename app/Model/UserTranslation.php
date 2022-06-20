<?php

namespace App\Model;



class UserTranslation extends MainModel
{
    protected $table = 'users_translation';
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
		"users_id",
		"firstname",
		"lastname",
		"nickname", 
		"address_line",
		"lang_code"
	];
	
	protected $appends = ["fullname"];
	
    public function getFullnameAttribute(){
        return "{$this->lastname} {$this->firstname}";
    }
}
