<?php

namespace App\Model;



class Contact extends MainModel
{
    protected $table = 'contact';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ["langs"];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        "id",
        "created_by_id",
        "updated_by_id",
        "created_date",
        "updated_date",
        "is_backup",
        "contact_type",//phone, mobile, address
        "value",
        "ordering",
        "parent_id",
        "icon",
        "channel"
    ];

    protected $casts = [
        "id" => "string",
        "is_backup" => "integer",
        "parent_id" => "string"
    ];

    public function langs(){
        return $this->hasMany('App\Model\ContactTranslation', 'contact_id', 'id');
    }
}
