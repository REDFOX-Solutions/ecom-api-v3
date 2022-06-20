<?php

namespace App\Model;



class Company extends MainModel
{
    protected $table = 'company';
    
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increase

    protected $with = ["langs"];//The relations to eager load on every query.
    protected $withCount = [];//The relationship counts that should be eager loaded on every query.

    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    protected $fillable = [
        "id",
        "district",
        "commune",
        "website",
        "domain",
        "facebook",
        "g_plus",
        "created_date",
        "updated_date",
        "email",
        "instagram",
        "pinterest",
        "twitter",
        "dribble",
        "city",
        "country",
        "phone",
        "mobile",
        "created_by_id",
        "updated_by_id",
        "is_active",
        "logo_link",
        "store_code",
        "is_backup",
        "aes_key",
        "other_phone",
        "postcode",
        "billing_country",
        "billing_city",
        "billing_commune",
        "billing_district",
        "billing_postcode",
        "company_type",
        "lat",
        "log",
        "light_logo",
        "dark_logo",
        "favorite_ico",
        "youtube",
        "linkedin",
        "map_embed_link",
        "industry",
        "industry_field",
        "ledger_id",
        "record_type_name",
        "record_type_id",
        "channels",
        "base_currency_id",
        "default_timezone",
        "main_branch_id",
        "score",
        "channels",
        "base_currency_id",
        "default_timezone",
        "light_land_logo",
        "dark_lang_logo"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer",
        "log"=> "double",
        "lat" => "double",
        "score" => "double"
    ];

    protected $appends = [
        'light_logo_preview', 
        'dark_logo_preview', 
        'favorite_ico_preview', 
        'products',
        'social_medias_r',
        'phones_c',
        'light_land_logo_preview',
        'dark_land_logo_preview'
    ];

    public static $relationship = [
        "children" => [ 
            ["name" => "contacts", "parent_field" => "parent_id", "controller" => "App\Http\Controllers\API\ContactController"]
        ]
    ];

    public function getProductsAttribute(){
        $comId = $this->id;
        // return [];

        return Products::whereRaw("find_in_set('".$comId."', store_ids)")
                        ->where("is_active", 1)
                        ->get()
                        ->toArray();
    }

    public function getLightLogoPreviewAttribute(){
        return $this->light_logo;
    }
    public function getDarkLogoPreviewAttribute(){
        return $this->dark_logo;
    }
    public function getLightLandLogoPreviewAttribute(){
        return $this->light_land_logo;
    }
    public function getDarkLandLogoPreviewAttribute(){
        return $this->dark_lang_logo;
    }
    public function getFavoriteIcoPreviewAttribute(){
        return $this->favorite_ico;
    }

    public function getSocialMediasRAttribute(){
        $lstContacts = Contact::where("contact_type", "social media")
                                ->where("parent_id", $this->id)
                                ->orderBy("ordering", "asc")
                                ->get()->toArray();

        return $lstContacts;
    }

    public function getPhonesCAttribute(){
        $lstContacts = Contact::where("contact_type", "phone")
                                ->where("parent_id", $this->id)
                                ->orderBy("ordering", "asc")
                                ->get()->toArray();

        return $lstContacts;
    }

    public function setRecordTypeNameAttribute($val){
        if(isset($val) && !isset($this->record_type_id)){
            $lstRecTyps = RecordType::where("name", $val)
                                    ->where("object_name", 'company')
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

    // public function getMapEmbedLinkAttribute($val){
    //     //get Google Api Key
    //     $lstApiKeys = ExternalApiKey::where("name", "google_map")
    //                                 ->where("is_active", 1)
    //                                 ->get()
    //                                 ->toArray();

    //     if(isset($lstApiKeys) && !empty($lstApiKeys)){
    //         $val .= '&key=' . $lstApiKeys[0]["api_key"];
    //     }
    //     return $val;
    // }
    
    /** relation table */ 
    
    /** child (relationship one to many)*/
    public function langs(){
        return $this->hasMany('App\Model\CompanyTranslation', 'company_id', 'id');
    }

    public function users(){
        return $this->hasMany('App\Model\User', 'company_id', 'id');
    }

    public function availableLanguages(){
        return $this->hasMany('App\Model\AvailableLanguages', 'company_id', 'id');
    }
    public function metaDataConfigs(){
        return $this->hasMany('App\Model\MetaDataConfig', 'company_id', 'id');
    }

    public function shopCurrencies(){
        return $this->hasMany('App\Model\ShopCurrency', 'company_id', 'id')
                    ->where("is_active",1 )
                    ->orderBy("is_base", "desc")
                    ->with("currencySheets");
    }
    
    public function ledger(){
        return $this->belongsTo('App\Model\AccountingBook', 'ledger_id');
    }
    public function baseCurrency(){
        return $this->belongsTo('App\Model\CurrencyPicklist', 'base_currency_id');
    }
    public function contacts(){
        return $this->hasMany('App\Model\Contact', 'parent_id', 'id');
    }
}
