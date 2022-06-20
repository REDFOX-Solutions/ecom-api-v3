<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Document extends MainModel
{
    protected $table = 'document';
   
    protected $primaryKey = 'id'; //table primary key column name, change it if it has different name
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increase

    protected $with = ["langs"];//The relations to eager load on every query.
    protected $withCount = [];//The relationship counts that should be eager loaded on every query.

    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    //allow which fill to add into DB
    protected $fillable = [
        "id", 
        "created_by_id", 
        "updated_by_id", 
        "created_date", 
        "updated_date", 
        "is_backup", 
        "author_id", 
        "body", 
        "content_type", 
        "is_public", 
        "keywords", 
        "extension", 
        "file_url", 
        "parent_id", 
        "downloads",
        "uploaded_date"
    ];

    //The attributes that should be cast to native types.
    protected $casts = [
        "is_backup" => "integer",
        "is_public" => "integer",
        "downloads" => "integer"
    ];

    // To autp create children records
    // public static $relationship = [
    //     "children" => [ 
    //         ["name" => "RELATIONSHIP_NAME", "parent_field" => "PARENT_FIELD", "controller" => "App\Http\Controllers\API\CHILD_CONTROLLER"]
    //     ]
    // ];
    
    
    /** relation table */

    /** parent (Relationship many to one)*/ 
    
    /** child (relationship one to many)*/
    public function langs(){
        return $this->hasMany(DocumentTranslate::class, 'document_id', 'id');
        
    } 
}
