<?php

namespace App\Model;



class ShipmentDetail extends MainModel
{
    protected $table = 'shipment_details';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ["saleDetail"];
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
        "products_id",
        "qty",
        "shipments_id",
        "sale_order_details_id",
        "sales_order_id",
        "ship_qty",
        "uom_id",
        "prev_shipped_qty",
        "prev_open_qty",
        "is_direct_create",
        "cogs_coa_id",
        "inventory_coa_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "qty" => "integer",
        "ship_qty" => "integer",
        "is_direct_create" => "integer",
        "prev_shipped_qty" => "double",
        "prev_open_qty" => "double",
    ];

    /** Block Relationship */
    public function saleOrder(){
        return $this->belongsTo('App\Model\SaleOrder', 'sales_order_id');
    }
    public function saleDetail(){
        return $this->belongsTo('App\Model\SaleOrderDetail', 'sale_order_details_id')->with("product");
    }
    public function shipment(){
        return $this->belongsTo('App\Model\Shipment', 'shipments_id');
    }
    public function product(){
        return $this->belongsTo('App\Model\Products', 'products_id');
    }

   

}
