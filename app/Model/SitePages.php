<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SitePages extends MainModel
{
    protected $table = 'site_pages';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs'];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id",
		"created_date",
		"updated_date",
		"created_by_id",
		"updated_by_id",
		"is_backup",
		"name",
        "image",
        "url",
        "icon",
        "is_visible"
    ];

    protected $casts = [
		"id" => "string", 
        'ordering' => 'integer',
        "is_backup" => "integer", 
        "is_visible" => "integer", 
    ];

    public function langs(){
        return $this->hasMany('App\Model\SitePageTranslation', 'site_pages_id', 'id');
    }

    public function sections(){
        return $this->hasMany('App\Model\Sections', 'site_pages_id', "id")->orderBy('ordering', "asc");
    }

    public function allSections(){
        return $this->hasMany('App\Model\Sections', 'site_pages_id', "id")->orderBy('ordering', "asc")->with(['subSections', 'assets']);
    }
}
