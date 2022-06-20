<?php

namespace App\Model;



class Categories extends MainModel
{
    protected $table = 'categories';
    
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increase

    protected $with = ["langs"];//The relations to eager load on every query.
    protected $withCount = [];//The relationship counts that should be eager loaded on every query.

    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    protected $fillable = [
        "id",
        "created_date",
        "updated_date",
        "created_by_id",
        "updated_by_id",
        "is_backup",
        "record_type_name",//it can be product, portfolio, article
        "record_type_id",
        "parent_id",
        "ordering",
        "item_type", 
        "image",
        "icon",
        "is_default",
        "slug",
        "not_delete",
        "code",
        "is_active",
        "property_ids",
        "is_favorite",
        "sub_category_ids",
        "name"
    ];

    protected $casts = [ 
        "is_backup" => "integer",
        "is_default" => "integer",
        "is_active" => "integer",
        "not_delete"=>"integer",
        "is_favorite" => "integer",
        "ordering" => "integer"
    ];

    protected $appends = ['image_preview', 'properties', 'sub_categories'];

    public function getImagePreviewAttribute(){
        return "{$this->image}";
    }

    public function getPropertiesAttribute(){
        $propertyIds = "{$this->property_ids}";
        if(isset($propertyIds) && !empty($propertyIds)){
            $lstIds = explode(",", $propertyIds);

            return Properties::whereIn('id', $lstIds)
                                // ->where("is_subcategory", 0)
                                ->orderBy("ordering", "asc")
                                ->get()
                                ->toArray();
        }
        return [];
    }

    public function getSubCategoriesAttribute(){
        $subCateIds = "{$this->sub_category_ids}";
        if(isset($subCateIds) && !empty($subCateIds)){
            $lstIds = explode(",", $subCateIds);

            return Properties::whereIn('id', $lstIds)
                                // ->where("is_subcategory", 1)
                                ->orderBy("ordering", "asc")
                                ->get()
                                ->toArray();
        }
        return [];
    }

    public function setRecordTypeNameAttribute($val){
        if(isset($val) && !isset($this->record_type_id)){
            $lstRecTyps = RecordType::where("name", $val)
                                    ->where("object_name", "categories")
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

    // public function getTotalProductAttribute(){
    //     // $count = ProductCategories::where('categories_id', $this->id)
    //     //             ->count();
    //     $count = Products::where('category_ids', 'like', '%'. $this->id .'%')->count();
    //     return $count; 
    // }

    /** relation table */

    /** parent (Relationship many to one)*/
    public function parent(){
        return $this->belongsTo('App\Model\Categories', "parent_id");
    }
    
    /** child (relationship one to many)*/
    public function langs(){
        return $this->hasMany('App\Model\CategoryTranslation', 'categories_id', 'id');
    }
 
    public function subCate(){
        return $this->hasMany('App\Model\Categories', 'parent_id',  'id');
    }

    public function subCategoriesR(){
        return $this->hasMany('App\Model\Categories', 'parent_id',  'id');
    }
    // public function productCategories(){
    //     return $this->hasMany('App\Model\ProductCategories', 'categories_id', 'id')->where('category_type', 'product');
    // }

    // public function postCategories(){
    //     return $this->hasMany('App\Model\PostCategory', 'categories_id', 'id')->where('category_type', 'post');
    // }

    public function productsR(){
        return $this->hasMany('App\Model\Products', "default_category_id", "id");
    }
    
}
