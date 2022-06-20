<?php

namespace App\Model;



class ReportSaleSummary extends MainModel
{
    
    protected $table = 'report_sale_summary';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        "id",
		"created_by_id",
		"updated_by_id",
		"created_date",
		"updated_date",
        "is_backup",
        "sale_date", 
        "total_qty", 
        "total_sub_amount",
        "total_discount",
        "total_vat_exempt", 
        "total_pax",
        "total_amount",
        "total_vat_taxable",
        "total_tax",
        "total_cost"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "total_qty" => "integer", 
        "total_amount" => "double",
        "total_sub_amount" => "double",
        "total_discount" => "double",
        "total_vat_exempt" => "double",
        "total_pax" => "integer",
        "total_vat_taxable" => "double",
        "total_tax" => "double",
        "total_cost" => "double"
    ]; 

    public function reportByChannels(){
        return $this->hasMany('App\Model\ReportSaleByChannel','sale_summary_id','id');
    }
    
    public  function detailByItems(){
        return $this->hasMany('App\Model\ReportSaleByItem','sale_summary_id','id');
    }

    public function detailByChannels(){
        return $this->hasMany('App\Model\ReportSaleByChannel','sale_summary_id','id');
    }
}
