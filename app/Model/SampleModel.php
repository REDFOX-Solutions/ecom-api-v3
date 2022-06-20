<?php

namespace App\Model;



class SampleModel extends MainModel
{
    protected $table = 'TABLE_NAME';
   
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
    ];

    //The attributes that should be cast to native types.
    protected $casts = [
        "is_backup" => "integer",
    ];

    // To autp create children records
    public static $relationship = [
        "children" => [ 
            ["name" => "RELATIONSHIP_NAME", "parent_field" => "PARENT_FIELD", "controller" => "App\Http\Controllers\API\CHILD_CONTROLLER"]
        ]
    ];
    
    
    /** relation table */

    /** parent (Relationship many to one)*/
    public function parent(){
        return $this->belongsTo('model path', "parent column name");
    }
    
    /** child (relationship one to many)*/
    public function langs(){
        return $this->hasMany('model path', 'foreing key column', 'primary key');
    }


    //formula fields
    protected $appends = [];//add custom formula field
    
    public function getCustomFieldAttribute(){
        return "value";
    }

    //to set value or convert value for create/update
    public function setFieldNameAttribute($value){
       $this->attributes['column_name'] = strtolower($value);
    }
    
    // For setup record type
    public function setRecordTypeNameAttribute($val){
        if(isset($val) && !isset($this->record_type_id)){
            $lstRecTyps = RecordType::where("name", $val)
                                    ->where("object_name", "TABLE_NAME")
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
}
