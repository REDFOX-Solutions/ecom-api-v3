<?php

namespace App\Model;

class Baskets extends MainModel
{
    protected $table = 'baskets';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
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
        "products_id",
        "qty", 
        "owner_id", //user id
        "basket_type", //value: main, addon
        "parent_baskets_id"//basket id (it will has value if type=addon)
    ];

    protected $casts = [
		"id" => "string",
		"is_backup" => "integer",
        "qty" => "integer"
    ];

    protected $appends = ['sale_price'];

    public function getSalePriceAttribute(){ 
        $lstPbe = PricebookEntry::where("products_id", "{$this->products_id}")
                    ->whereHas("pricebook", function($query){
                        $query->where('is_standard', 1)->where('is_active', 1);
                    })
                    ->limit(1)
                    ->get()
                    ->toArray();
        
        return count($lstPbe) > 0 ? $lstPbe[0]['unit_price']: 0;
    }

    public function owner(){
        return $this->belongsTo('App\Model\User', 'owner_id');
    } 
  
    public function product(){
        return $this->belongsTo('App\Model\Products', 'products_id');
    }
}
