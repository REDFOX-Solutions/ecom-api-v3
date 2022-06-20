<?php

namespace App\Model;


class ProductPropertyTranslation extends MainModel
{
    protected $table = 'prod_prop_translate';
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
        "value",
        "product_properties_id",
    ];

    protected $casts = [
		"id" => "string", 
		"is_backup" => "integer", 
		"product_properties_id" => "string", 
    ];
    
    protected $appends = ["values_c"]; 

    public function getValuesCAttribute(){
        return explode(";", $this->value);
    }
}
