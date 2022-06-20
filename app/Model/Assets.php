<?php

namespace App\Model;

class Assets extends MainModel
{
    protected $table = 'assets';
    
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increase

    protected $with = ["langs"];//The relations to eager load on every query.
    protected $withCount = [];//The relationship counts that should be eager loaded on every query.

    //protected $dateFormat = 'Y-m-dTH:i:s';// * The storage format of the model's date columns.
    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    protected $fillable = [
        "id", 
        "created_date",
        "updated_date",
        "created_by_id",
        "updated_by_id",
        "icon",
        "image",
        "ordering",
        "type",
        "edit_icon",
        "edit_title",
        "edit_subtitle",
        "edit_short_desc",
        "edit_image",
        "progress_value",
        "is_progress",
        "sections_id",
        "is_backup",
        "parent_id",
        "record_type",
        "record_type_id",
        "internal_link",
        "external_link",
        "is_external"
    ];

    protected $casts = [
		"id" => "string",
		'ordering' => 'integer', 
        "edit_icon" => "integer",
        "edit_title" => "integer",
        "edit_subtitle" => "integer",
        "edit_short_desc" => "integer",
        "edit_image" => "integer",
        "is_progress" => "integer",
        "is_backup" => "integer",
        "is_external" => "integer"
    ];

    /** relation table */

    /** parent (Relationship many to one)*/
    public function section(){
        return $this->belongsTo('App\Model\Sections', "sections_id");
    }
    
    /** child (relationship one to many)*/
    public function langs(){
        return $this->hasMany('App\Model\AssetTranslation', 'assets_id', 'id');
    }

    protected $appends = ['image_preview'];

	public function getImagePreviewAttribute(){
        return "{$this->image}";
	}
}
