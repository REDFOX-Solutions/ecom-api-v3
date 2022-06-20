<?php

namespace App\Model;



class Shipment extends MainModel
{
    protected $table = 'shipments';
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
        "invoices_id",
        "ship_to_id", //location id
        "ship_by_id",
        "received_by_id",
        "ship_datetime", 
        "status", //hold, open, completed
        "ship_num",
        "sales_order_id",
        "total_qty",
        "ship_type",
        "orig_ship_id",
        "issue_prod_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "total_qty" => "integer"
    ];

    protected $appends = ["order_count"];

    public function getOrderCountAttribute() {
        $orderId = $this->id;
        $count = ShipmentDetail::where("shipments_id", $orderId)->get()->groupBy("sales_order_id")->count();
        return $count;
    }

    public function shipTo(){
        return $this->belongsTo('App\Model\Locations', 'ship_to_id')->with("parent");
    }
    public function shipBy(){
        return $this->belongsTo('App\Model\PersonAccount', 'ship_by_id');
    } 
    public function receivedBy(){
        return $this->belongsTo('App\Model\PersonAccount', 'received_by_id');
    } 
    public function details(){
        return $this->hasMany('App\Model\ShipmentDetail', 'shipments_id', 'id')->with(["product", "saleOrder"]);
    }
    public function invoice(){
        return $this->belongsTo('App\Model\Invoices', "invoices_id")->with("invoiceReceipts");
    }
    public function createdBy(){
        return $this->belongsTo('App\Model\User', 'created_by_id');
    }
    public function invoices(){
        return $this->belongsTo('App\Model\Invoices', 'invoices_id');
    } 
}
