<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SiteNav extends MainModel
{
    protected $table = 'site_nav';
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
		"ordering",
        "target",
        "site_nav_id", 
        "icon", 
        "is_divider", 
        "is_header",
        "is_external",
        "is_visible"
    ];

    protected $casts = [
		"id" => "string", 
        'ordering' => 'integer',
        "is_backup" => "integer", 
        "is_divider" => "integer", 
        "is_header" => "integer",
        "is_external" => "integer",
        "is_visible" => "integer"
    ];

    public function langs(){
        return $this->hasMany('App\Model\SiteNavTranslation', 'site_nav_id', 'id');
    }

    public function subMenus(){
        return $this->hasMany('App\Model\SiteNav', 'site_nav_id', "id")->with('subMenus')->orderBy("ordering", "asc");
    }

    public function parentMenu(){
        return $this->belongsTo('App\Model\SiteNav', 'site_nav_id');
    }
}
