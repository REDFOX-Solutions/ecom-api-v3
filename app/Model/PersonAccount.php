<?php

namespace App\Model;



class PersonAccount extends MainModel
{
    protected $table = 'person_account';
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
		"created_by_id", 
		"updated_date", 
		"updated_by_id", 
		"phone", 
		"email", 
		"commune", 
		"district", 
		"city", 
		"postalcode", 
		"country", 
		"billing_commune", 
		"billing_district", 
		"billing_city", 
		"billing_postalcode", 
		"billing_country", 
		"person_type", //customer, agent,
		"is_backup", 
		"person_code", 
		"users_id", 
		"photo", 
		"is_active", 
		"birthday", 
		"gender", 
		"date_hired", 
		"date_leave", 
		"facebook", 
		"twitter", 
		"pinterest", 
		"google_plus", 
		"mobile", 
		"other_phone", 
		"linkedin", 
		"title", 
		"marital_status", 
		"nationality", 
		"ethnicity", 
		"religion", 
		"birth_commune", 
		"birth_district", 
		"birth_city", 
		"birth_country", 
		"status", 
		"home_phone",
		"partner_company_id",
		"register_date",
		"height", 
		"house_num", 
		"couple_divided", 
		"count_son", 
		"count_daughter", 
		"birth_house_num", 
		"is_alive", 
		"birth_village", 
		"village", 
		"fax", 
		"website",
		"ordering",
		"show_on_footer",
		"record_type_id",
		"record_type",
		"is_default",
		"company_id",
		"lat",
		"log",
		"personal_coa_id",
		"discount_coa_id",
		"prepayment_coa_id",
		"retainage_coa_id",
		"passport"
	];
	
    protected $casts = [
		"id" => "string",
		"is_backup" => "integer",
		"is_active" => "integer",
		"is_alive" => "integer",
		"count_son" => "integer",
		"count_daughter" => "integer",
		"ordering" => "integer",
		"show_on_footer" => "integer",
		"is_default" => "integer",
		"lat" => "double",
		"log" => "double"
	];

	protected $appends = ['photo_preview', 'passport_preview'];

	public static $relationship = [
        "children" => [  
			["name" => "contacts", "parent_field" => "parent_id", "controller" => "App\Http\Controllers\API\ContactController"],
            ["name" => "social_medias_r", "parent_field" => "parent_id", "controller" => "App\Http\Controllers\API\ContactController"],
			["name" => "photos", "parent_field" => "parent_id", "controller" => "App\Http\Controllers\API\PhotoController"],
        ]
    ];

	public function getPhotoPreviewAttribute(){
        return "{$this->photo}";
	}

	public function getPassportPreviewAttribute(){
        return "{$this->passport}";
	}

	public function setRecordTypeAttribute($val){
        if(isset($val) && !isset($this->record_type_id)){
            $lstRecTyps = RecordType::where("name", $val)
                                    ->where("object_name", "person_account")
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
	
    public function langs(){
        return $this->hasMany('App\Model\PersonAccountTranslation', 'person_account_id', 'id');
	}

	// TO be delete, not sure which project is using it
	public function contact(){
        return $this->hasMany('App\Model\Contact', 'parent_id', 'id');
	}
    public function contacts(){
        return $this->hasMany('App\Model\Contact', 'parent_id', 'id');
	}
	
	public function receipt() {
		return $this->belongsTo('App\Model\Receipts', 'id', "received_from_id");
	}
	public function chartOfAccount(){
		return $this->belongsTo('App\Model\ChartOfAccount','personal_coa_id');
	}

	public function photos(){
		return $this->hasMany('App\Model\Photos', 'parent_id', 'id');
	}

	public function socialMediasR(){
		return $this->hasMany('App\Model\Contact', 'parent_id', 'id')
					->where("contact_type", "social media")
					->orderBy("ordering", "asc");
	}
}
