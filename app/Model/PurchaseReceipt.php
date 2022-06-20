<?php

namespace App\Model;


use App\Model\PurchaseReceiptDetail;
use App\Model\PurchaseBill;
use App\Model\PurchaseBillPayment;

class PurchaseReceipt extends MainModel
{
    protected $table = 'purchase_receipts';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ["langs"];
    // protected $with = ["langs", "users", "vendor"];
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
        "receipt_num",
        "vendor_id",  
        "is_create_bill",
        "pr_type",
        "location_id",
        "received_date",
        "delivery_by_id",
        "status", 
        "accepted_by_id",
        "purchase_bills_id",
        "orig_receipt_id",
        "received_by_id",
        "total_qty",
        "total_cost",
        "has_return",
        "record_type_name",
        "record_type_id",
        "receipt_prod_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "has_return" => "integer",
        "is_create_bill" => "integer",
        "total_qty" => "double",
        "total_cost" => "double",
        "control_qty" => "double",
        "total_purchase_order" => "integer",
        "total_purchase_bill" => "integer"
    ];

    protected $appends = [
        'total_purchase_order',
        'total_purchase_receipt_detail',
        'total_purchase_bill', 'count_payments'
    ];
    
    public function getTotalPurchaseOrderAttribute(){
        $prID = $this->id;
        $count_records = PurchaseReceiptDetail::where("purchase_receipts_id", $prID)->get()
                        ->groupBy("purchase_order_id")
                        ->count();
        return  $count_records;

    }
    public function getTotalPurchaseReceiptDetailAttribute(){
        $prID = $this->id;
        $count_records = PurchaseReceiptDetail::where("purchase_receipts_id", $prID)->get()
                        ->groupBy("purchase_receipts_id")
                        ->count();
        return  $count_records;

    }
    public function getTotalPurchaseBillAttribute(){
        $pbID = $this->purchase_bills_id;
        $count_records = PurchaseBill::where("id", $pbID)->get()
                        ->groupBy("id")
                        ->count();
        return  $count_records;

    }

    public function getCountPaymentsAttribute(){
        $pbID = $this->purchase_bills_id;
        $count_records = PurchaseBillPayment::where("purchase_bills_id", $pbID)->get()
                        ->groupBy("purchase_payments_id")
                        ->count();
        return  $count_records;

    }

    public function langs(){
        return $this->hasMany('App\Model\PurchaseReceiptTranslaton', 'purchase_receipts_id', 'id');
    }

    public function vendor(){
        return $this->belongsTo('App\Model\PersonAccount', 'vendor_id', 'id');
    }
    public function bill(){
        return $this->belongsTo('App\Model\PurchaseBill', 'purchase_bills_id', 'id');
    }
    public function billToPayment(){
        return $this->belongsTo('App\Model\PurchaseBill', 'purchase_bills_id', 'id')->with("billPayments");
    }

    public function receiptDetails(){
        return $this->hasMany('App\Model\PurchaseReceiptDetail', 'purchase_receipts_id', 'id')->with(["purchaseOrder", "orderDetails"]);
    }
    public function users(){
        return $this->belongsTo('App\Model\User', 'received_by_id');
    }
    public function receiptBy(){
        return $this->belongsTo('App\Model\PersonAccount', 'received_by_id');
    }
    public function deliveryBy(){
        return $this->belongsTo('App\Model\PersonAccount', 'delivery_by_id');
    }
}
