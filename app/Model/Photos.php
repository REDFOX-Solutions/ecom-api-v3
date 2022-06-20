<?php

namespace App\Model;



class Photos extends MainModel
{
    protected $table = 'photos';
    protected $keyType = 'string';
    public $incrementing = false; 
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
        "parent_id", 
        "ordering", 
        "thumbnail", 
        "photo",
        "record_type_id",
        "record_type_name",
        "category_id",
        "is_pano"
    ]; 

    protected $casts = [
		"id" => "string",
		'parent_id' => 'string',
		"is_backup" => "integer",
        "ordering" => "integer",
        "is_pano" => "integer"
    ];

    protected $appends = ['photo_preview', 'thumbnail_preview'];

    public function setRecordTypeNameAttribute($val){
        if(isset($val) && !isset($this->record_type_id)){
            $lstRecTyps = RecordType::where("name", $val)
                                    ->where("object_name", "photos")
                                    ->get()
                                    ->toArray();
            if(isset($lstRecTyps) && !empty($lstRecTyps)){
                $recTyp = $lstRecTyps[0];
                $this->attributes["record_type_id"] = $recTyp["id"];
            }                                    
            
        }

        $this->attributes["record_type_name"] = $val;
    }

    public function setRecordTypeIdAttribute($val){

        if(isset($val) && !isset($this->record_type_name)){
            $lstRecTyps = RecordType::where("id", $val)
                                    ->get()
                                    ->toArray();

            if(isset($lstRecTyps) && !empty($lstRecTyps)){
                $recTyp = $lstRecTyps[0];
                $this->attributes["record_type_name"] = $recTyp["name"];
            } 
        }

        $this->attributes["record_type_id"] = $val;
    }

    public function getPhotoPreviewAttribute(){
        return "{$this->photo}";
    }

    public function getThumbnailPreviewAttribute(){
        return "{$this->thumbnail}";
    }
}
