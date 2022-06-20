<?php

namespace App\Model;



class ShiftSaleRevenue extends MainModel
{
    protected $table = 'shift_sale_revenue';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['category'];
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
        "category_id",  
        "category_name",
        "amount", 
        "staff_shifts_id",
        "transaction_date"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "amount" => "double"
    ];

    protected $appends = [];

    public function setCategoryIdAttribute($val){
        $lstCates = Categories::where("id", $val)
                                ->get()
                                ->toArray();

        if(isset($lstCates) && !empty($lstCates)){
            $this->attributes["category_name"] = isset($lstCates[0]["name"]) ? $lstCates[0]["name"]: $val;
        }
        $this->attributes["category_id"] = $val;
    }

    public function category(){
        return $this->belongsTo('App\Model\Categories', 'category_id');
    }

    public function staffShift(){
        return $this->belongsTo('App\Model\StaffShifts', 'staff_shifts_id');
    } 
}
