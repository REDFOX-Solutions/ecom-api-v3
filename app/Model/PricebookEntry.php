<?php

namespace App\Model;

use App\Services\GlobalStaticValue;
use Carbon\Carbon;

class PricebookEntry extends MainModel
{
    protected $table = 'pricebook_entry';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
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
        "pricebook_id",
        "products_id",
        "is_active", 
        "unit_price",
        "is_default",
        "regular_price",
        "desc",
        "msrp",
        "min_markup_rate", 
        "markup_rate"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "is_active" => "integer",
        "unit_price" => "double",
        "is_default" => "integer",
        "regular_price" => "double",
        "msrp" => "double",
        "min_markup_rate" => "double",
        "markup_rate" => "double",
        "unit_price_c" => "double"
    ];

    protected $appends = [
        'unit_price_c' // this price is calculate with planer
    
    ];
 
    public function product(){
        return $this->belongsTo('App\Model\Products', 'products_id');
    }

    public function pricebook(){
        return $this->belongsTo('App\Model\Pricebook', 'pricebook_id');
    }

    public function getUnitPriceCAttribute(){
        $now = Carbon::now()->format(GlobalStaticValue::$FORMAT_DATE); 
        $unitPrice = isset($this->unit_price) ? $this->unit_price : 0;
        $lstPricePlaners = PricebookEntryPlaner::where("pbe_id", $this->id)
                                                ->whereDate("start_date", "<=", $now)
                                                ->where(function($query) use ($now){
                                                    $query->whereNull("end_date")
                                                        ->orWhereDate("end_date", ">=", $now);
                                                })
                                                ->get()->toArray();
        
        //if there are price planer setup for today, we return the price from 
        // planner
        if(!empty($lstPricePlaners)){
            $currentPrice = $lstPricePlaners[0]; //it will always has 1 valid price for pricebook entry and current date
            $isIncreate = $currentPrice["is_increase"] == 1;
            $isRatePercent = $currentPrice["rate_type"] == 'percent';
            $rate = $currentPrice["rate"];
            
            //calc to get rate amount,
            // if rate type is percent, it means the value in Rate field is for percent
            $rateAmt = $isRatePercent ? ($unitPrice * $rate) : $rate;
            $unitPrice = $isIncreate ? ($unitPrice + $rateAmt) : ($unitPrice - $rateAmt);
        }

        return $unitPrice;
    }
}
