<?php

namespace App\Model;

class PurchaseBill extends MainModel
{
    protected $table = 'purchase_bills';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ["langs"];
    // protected $with = ["langs", "vendor"];
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
        "bill_num",
        "bill_to_id",  
        "status",
        "amount_paid",
        "bill_date",
        "vendor_id",
        "sub_total",
        "total_discount", 
        "vat_exem_total",
        "vat_tax_total",
        "balance",
        "due_balance",
        "total_balance",
        "vendor_location_id",
        "bill_type",
        "vendor_coa_id",
        "bill_from"//accounting, purchase
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "amount_paid" => "double",
        "sub_total" => "double",
        "total_discount" => "double",
        "vat_exem_total" => "double",
        "vat_tax_total" => "double",
        "balance" => "double",
        "due_balance" => "double",
        "total_balance" => "double"
    ];

    protected $appends = ['due_balance'];
    //** block Farmula fields */
    public function getDueBalanceAttribute(){
      $duebalance = $this->total_balance - $this->balance;
        return  ($duebalance < 0 ? 0 : $duebalance);
    }

     //** block set defuls field */
    // public function setBalanceAttribute($val){
    //     if(isset($this->total_balance) &&
    //         isset($val) &&
    //         $val > 0 &&
    //         $this->total_balance > 0 &&
    //         $this->total_balance == $val )
    //     {
    //         $this->attributes['status'] = 'completed';
    //     }
    //     $this->attributes['balance'] = $val;
    // }

    public function langs(){
        return $this->hasMany('App\Model\PurchaseBillTranslation', 'purchase_bills_id', 'id');
    }

    public function purchaseReceipts(){
        return $this->hasMany('App\Model\PurchaseReceipt', 'purchase_bills_id', 'id');
    }
    public function billDetails(){
        return $this->hasMany('App\Model\PurchaseBillDetail', 'bill_id', 'id')->with("products");
    }

    public function purchaseBillPayment(){
        return $this->hasMany('App\Model\PurchaseBillPayment', 'purchase_bills_id', 'id')->with(["payments"]);
    }

    public function billPayments(){
        return $this->belongsTo('App\Model\PurchaseBillPayment', 'id', 'purchase_bills_id')->with(["payments"]);
    }

    public function vendor(){
        return $this->belongsTo('App\Model\PersonAccount', 'vendor_id', 'id');
    }
}
