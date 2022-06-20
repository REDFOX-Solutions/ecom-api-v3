<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HotelAmenities extends MainModel
{
    protected $table = 'hotel_amenities';
   
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
        "record_type_id", 
        "record_type_name", 
        "is_active", 
        "ordering", 
        "category_id", 
        "photo", 
        "icon"
    ];

    //The attributes that should be cast to native types.
    protected $casts = [
        "is_backup" => "integer",
        "is_active" => "integer", 
        "ordering" => "integer", 
    ]; 


    public function setRecordTypeNameAttribute($val){
        if(isset($val) && !isset($this->record_type_id)){
            $lstRecTyps = RecordType::where("name", $val)
                                    ->where("object_name", "hotel_amenities")
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


    /**
     * Get the record type that owns the HotelAmenities
     */
    public function recordType()
    {
        return $this->belongsTo(RecordType::class, 'record_type_id');
    }

    /**
     * Get the category that owns the HotelAmenities 
     */
    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    /**
     * Get all of the langs for the HotelAmenities
     */
    public function langs(){
        return $this->hasMany(HotelAmenitiesTranslate::class, 'hotel_amenities_id', 'id');
    }

    /**
     * Get all of the photos for the HotelAmenities
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(Photos::class, 'parent_id', 'id');
    }
}
