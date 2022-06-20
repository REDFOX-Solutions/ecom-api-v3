<?php

namespace App\Model;



class GlobalPicklist extends MainModel
{
    protected $table = 'global_picklist';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
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
        "label", 
        "name",
        "values",// multiple value by semi colum ";"
        "description", 
        "parent_id",
        "company_id",
        "icon",
        "picklist_type",
        "is_active",
        "ordering",
        "code"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer",
        "ordering" => "integer"
    ];

    protected $appends = []; 
    
    public function sub_picklists(){
        return $this->hasMany('App\Model\GlobalPicklist', 'parent_id', 'id');
    }

    public function parent(){
        return $this->belongsTo('App\Model\GlobalPicklist', 'parent_id');
    }

}
