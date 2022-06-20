<?php

namespace App\Model; 

class Products extends MainModel
{
    protected $table = 'products';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs', 'recordType'];
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
        "photo",
        "is_backup", 
        "is_active", 
        "type", 
        "is_new", 
        "has_stock", 
        "best_sell",
        "slug", 
        "tag_ids", 
        "frequency_ids", 
        "is_comming", 
        "status",
        "video_link",
        "has_video",
        "is_recommend",
        "code",
        "sku",
        "record_type_id",
        "record_type_name",
        "item_type", 
        "min_stock",
        "max_stock",
        "tags", 
        "default_category_id",
        "is_kit",
        "base_uom",
        "cost_method",// type of costing: standard, avg
        "cost",
        "option_master_id", 
        "addon_ids",
        "category_ids",  
        "for_pos_sale",
        "for_sale",
        "for_expense",
        "brand",
        "ind_brand",
        "model",
        "year",  
        "store_ids", 
        "inventory_coa_id",
        "sale_coa_id",
        "cogs_coa_id",
        "std_cost_var_coa_id",
        "std_cost_rev_coa_id",
        "po_accrual_coa_id",
        "deferral_coa_id",
        "score",
        "related_product_ids",
        "thumbnail",
        "ordering"
    ];
    /**
     * Other auto fields
     * cost_effective_date // it use to create standard cost
     */

    protected $appends = [
                            'photo_preview',
                            'thumbnail_preview',
                            'standard_unitprice', 
                            'standard_cost',
                            'standard_regularprice',
                            'frequencies', 
                        ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "is_active" => "integer",
        "is_new" => "integer",
        "has_stock" => "integer",
        "best_sell" => "integer",
        "is_comming" => "integer",
        "has_video" => "integer",
        "is_recommend" => "integer",
        "standard_unitprice" => "double",
        "standard_cost" => "double",
        "for_pos_sale" => "integer",
        "for_sale" => "integer",
        "for_expense" => "integer",
        "cost" => "double",
        "year" => "integer",
        "standard_regularprice" => "double",
        "min_stock" => "double",
        "max_stock" => "double",
        "is_kit" => "integer",
        "cost" => "double",
        "standard_sale_price" => "double",
        "score" => "double",
        "ordering" => "integer"
    ];

    
 
                         // To autp create children records
    public static $relationship = [
        "children" => [ 
            ["name" => "properties", "parent_field" => "products_id", "controller" => "App\Http\Controllers\API\ProductPropertyController"],
            ["name" => "options_r", "parent_field" => "option_master_id", "controller" => "App\Http\Controllers\API\ProductsController"],
            ["name" => "photos", "parent_field" => "parent_id", "controller" => "App\Http\Controllers\API\PhotoController"]
        ]
    ];

    public function getStandardRegularpriceAttribute(){
              $lstPbe = PricebookEntry::where("products_id", "{$this->id}")
                                ->where("is_default", 1) 
                                ->where("is_active", 1)  
                                ->get()
                                ->toArray();
        
        return empty($lstPbe) ? 0 : $lstPbe[0]['regular_price'];
    }

    

    public function getPhotoPreviewAttribute(){
        return "{$this->photo}";
    }
    public function getThumbnailPreviewAttribute(){
        return "{$this->thumbnail}";
    }

    public function getFrequenciesAttribute()
    {
        $frequencyid = $this->frequency_ids;
        $lstFrequencyId = explode(",", $frequencyid);

        return Products::where($lstFrequencyId);

    }
    public function getStandardUnitpriceAttribute(){ 

        $lstPbe = PricebookEntry::where("products_id", "{$this->id}")
                                ->where("is_default", 1) 
                                ->where("is_active", 1) 
                                ->get()
                                ->toArray();
        
        $unitprice = 0;
        //use unit_price_c because it already calc with price planer
        if(!empty($lstPbe) && isset($lstPbe[0]['unit_price_c'])){
            $unitprice = $lstPbe[0]['unit_price_c'];
        }

        return empty($lstPbe) ? 0 : $lstPbe[0]['unit_price'];
    }

    public function getStandardCostAttribute(){ 
        return "{$this->cost}"; 
    }



    public function langs(){
        return $this->hasMany('App\Model\ProductTranslation', 'products_id', 'id');
    }

    public function recordType(){
        return $this->belongsTo('App\Model\RecordType', 'record_type_id');
    }

    public function photos(){
        return $this->hasMany('App\Model\Photos', 'parent_id', 'id');
    }

    public function pricebookEntries(){
        return $this->hasMany('App\Model\PricebookEntry', 'products_id', 'id')->with("pricebook");
    }

    public function productCostStatistics()
    {
        return $this->hasMany('App\Model\ProductCostStatistics','products_id','id');
    }

    // public function productGlaccount()
    // {
    //     return $this->hasMany('App\Model\ProductGLAccount','products_id','id');
    // }

    public function activeCosts(){
        return $this->hasMany('App\Model\ProductStandardCost', 'products_id', "id")->where("is_active", 1);
    }

    public function properties(){
        return $this->hasMany('App\Model\ProductProperty', 'products_id', "id")->with(["property"])->orderBy("ordering", "asc");
    }

    public function glAccounts(){
        return $this->hasMany('App\Model\GLAccMapping', 'obj_record_id', 'id')->where("is_active", 1);
    }

    public function category(){
        return $this->belongsTo('App\Model\Categories', 'default_category_id');
        
    }

    public function store(){
        return $this->belongsTo('App\Model\Company', 'store_ids');
    }

    public function products(){
        return $this->hasMany('App\Model\Products', 'option_master_id', 'id');
    }

    public function optionsR(){
        return $this->hasMany('App\Model\Products', 'option_master_id', 'id')->with("properties");
    }
    
}