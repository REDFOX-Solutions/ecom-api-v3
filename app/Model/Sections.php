<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Sections extends MainModel
{
    protected $table = 'sections';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs'];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id",
		"name",
		"type",
		"bg_img",
		"bg_color",
		"created_date",
		"updated_date",
		"bg_video",
		"created_by_id",
		"updated_by_id",
		"is_backup",
        "parent_id",
        "ordering",
        "edit_bg_image",
        "site_pages_id",
        "record_type",
        "record_type_id",
        "add_asset",
        "is_visible",
        "code"

    ];

    protected $casts = [
		"id" => "string", 
        'parent_id' => 'string',
        "is_backup" => "integer", 
        "ordering" => "integer",
        "add_asset" => "integer",
        "is_visible" => "integer"
    ];

    protected $appends = ['bg_img_preview'];

    public static $relationship = [
        "children" => [ 
            ["name" => "assets", "parent_field" => "sections_id", "controller" => "App\Http\Controllers\API\AssetsController"],
            ["name" => "sections", "parent_field" => "parent_id", "controller" => "App\Http\Controllers\API\SectionsController"]
        ]
    ];

	public function getBgImgPreviewAttribute(){
        return "{$this->bg_img}";
	}

    public function parent(){
        return $this->belongsTo('App\Model\Sections', 'parent_id');
    }

    public function langs(){
        return $this->hasMany('App\Model\SectionTranslation', 'sections_id', 'id');
    } 

    public function assets(){
        return $this->hasMany('App\Model\Assets', 'sections_id', 'id')->orderBy('ordering', "asc");
    }

    public function sections(){
        return $this->hasMany('App\Model\Sections', 'parent_id', 'id')->orderBy('ordering', "asc");
    }

    public function sectionsAssets(){
        return $this->hasMany('App\Model\Sections', 'parent_id', 'id')->orderBy('ordering', "asc")->with('assets');
    }

    public function subSections(){
        return $this->hasMany('App\Model\Sections', 'parent_id', 'id')->orderBy('ordering', "asc")->with(['subSections', 'assets']);
    }
}
