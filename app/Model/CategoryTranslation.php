<?php

namespace App\Model;



class CategoryTranslation extends MainModel
{
    protected $table = 'category_translation';
    
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increase

    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    protected $fillable = [
        "id",
        "created_date",
        "created_by_id",
        "updated_date",
        "updated_by_id",
        "is_backup",
        "lang_code",
        "categories_id",
        "name",
        "note",
        "label",
        "short_desc"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer"
    ];
}
