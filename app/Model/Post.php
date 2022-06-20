<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends MainModel
{
    protected $table = 'posts';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs'];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    public static $statusVal = ['draft' => 'draft', 'published' => 'published'];
    protected $fillable = [
        "id", 
        "created_date", 
        "created_by_id", 
        "updated_date", 
        "updated_by_id", 
        "is_backup", 
        "is_draft", 
        "author_id", 
        "posted_at", 
        "code", 
        "main_photo", 
        "status", //draft, publised
        "slug", 
        "category_ids", 
        "tag_ids", 
        "media_link", //it is video link
        "is_top", 
        "is_new",
        "ordering",
        "posts_id",
        "is_video", 
        "is_active",
        "is_external_link",
        "record_type_id",
        "record_type",
        "expiry_date",
        "company_id",
        "score"
    ];

    protected $casts = [
		"id" => "string",
		'author_id' => 'string',
		"is_backup" => "integer",
        "is_draft" => "integer",
        "is_top" => "integer", 
        "is_new" => "integer",
        "ordering" => "integer",
        "is_video" => "integer",
        "is_active" => "integer",
        "is_external_link" => "integer",
        "code" => "integer",
        "score" => "double"
        
    ];

    protected $appends = ['main_photo_preview', "categories"];

    public function getMainPhotoPreviewAttribute(){
        return "{$this->main_photo}";
    }

     

    public function author(){
        return $this->belongsTo('App\Model\User', 'author_id');
    }

    public function langs(){
        return $this->hasMany('App\Model\PostTranslation', 'posts_id', 'id');
    } 

    //to set format field
    public function setRecordTypeAttribute($val){
        if(isset($val) && !isset($this->record_type_id)){
            $lstRecTyps = RecordType::where("name", $val)
                                    ->where("object_name", "posts")
                                    ->get()
                                    ->toArray();
            if(isset($lstRecTyps) && !empty($lstRecTyps)){
                $recTyp = $lstRecTyps[0];
                $this->attributes["record_type_id"] = $recTyp["id"];
            }                                    
            
        }

        $this->attributes["record_type"] = $val;
    }

    public function setRecordTypeIdAttribute($val){

        if(isset($val) && !isset($this->record_type_name)){
            $lstRecTyps = RecordType::where("id", $val)
                                    ->get()
                                    ->toArray();

            if(isset($lstRecTyps) && !empty($lstRecTyps)){
                $recTyp = $lstRecTyps[0];
                $this->attributes["record_type"] = $recTyp["name"];
            } 
        }

        $this->attributes["record_type_id"] = $val;
    }

    public function setAuthorIdAttribute($value){
        $this->attributes['author_id'] = Auth::user()->id;
    }

    public function photos(){
        return $this->hasMany('App\Model\Photos', 'parent_id', "id");
    }

    public function subPosts(){
        return $this->hasMany('App\Model\Post', 'posts_id', 'id');
    }

    public function documents(){
        return $this->hasMany('App\Model\Documents', 'parent_id', 'id');
    }

    public function createdBy(){
        return $this->belongsTo('App\Model\User', 'created_by_id');
    }

    public function updatedBy(){
        return $this->belongsTo('App\Model\User', 'updated_by_id');
    }
    public function posts(){
        return $this->belongsTo('App\Model\Post', 'posts_id');
    }

    public function getCategoriesAttribute(){
        $categoryIds = $this->category_ids; 
        $lstCategoryIds = explode(",", $categoryIds);
        return Categories::whereIn("id", $lstCategoryIds)->get(); 
    }
}
