<?php

namespace App\Model;



class StaffShifts extends MainModel
{
    protected $table = 'staff_shifts';
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
        "start_datetime", 
        "end_datetime", 
        "closed_by_id", //object user
        "approved_by_id", //object user
        "total_theory_collection", 
        "total_actual_collection", 
        "total_discount", 
        "total_sale_rev", 
        "over_short", 
        "staff_end_day_id",
        "note",
        "transaction_date",
        "unbilled_amount",
        "owner_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "total_theory_collection" => "double",
        "total_actual_collection" => "double",
        "total_discount" => "double",
        "total_sale_rev" => "double",
        "over_short" => "double",
        "unbilled_amount" => "double"
    ];

    protected $appends = [];

    public function staffEndDay(){
        return $this->belongsTo('App\Model\StaffEndDay', 'staff_end_day_id');
    } 

    public function shiftSaleRevenue(){
        return $this->hasMany('App\Model\ShiftSaleRevenue', 'staff_shifts_id', 'id');
    }

    public function saleOrder(){
        return $this->hasMany('App\Model\SaleOrder','staff_shifts_id','id');
    }
}
