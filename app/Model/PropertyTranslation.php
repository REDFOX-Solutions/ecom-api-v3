<?php

namespace App\Model;

class PropertyTranslation extends MainModel
{
    protected $table = 'property_translation';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [ 
        "id",
		"created_by_id",
		"created_date",
		"updated_by_id",
		"updated_date",
		"is_backup",
		"lang_code",
		"name",
        "properties_id",
        "value_options",
        "note"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "properties_id" => "string"
    ];

    protected $appends = ["value_options_c"]; 

    public function getValueOptionsCAttribute(){
        return explode(";", $this->value_options);
    }
}