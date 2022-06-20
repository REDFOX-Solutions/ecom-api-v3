<?php

namespace App\Model;



class Invoices extends MainModel
{
    protected $table = 'invoices';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs'];
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
        "bill_to_id", //customer id
        "invoice_num",  
        "status", //hold, open, completed
        "amount_paid",
        "inv_date", 
        "record_type_id", 
        "grand_total",
        "penalty_amount",
        "inv_type",
        "sub_total",
        "discount_total",
        "discount_rate",
        "tax_total",
        "customer_coa_id",
        "source"//accounting, sale
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "amount_paid" => "double",
        "grand_total" => "double",
        "penalty_amount" => "double",
        "sub_total" => "double",
        "discount_total" => "double",
        "discount_rate" => "double",
        "tax_total" => "double"
    ];

    //Block Formula fields
    protected $appends = ['due_balance'];

	public function getDueBalanceAttribute(){
        $dueBalance = $this->grand_total - $this->amount_paid;
        return ($dueBalance < 0 ? 0 : $dueBalance);
	}
    
    //Block set default field
    public function setAmountPaidAttribute($val){
        //set field status value
        if(isset($this->grand_total) && 
            isset($this->amount_paid) && 
            $this->amount_paid > 0 &&
            $this->grand_total > 0 &&
            $this->grand_total == $this->amount_paid )
        {
            $this->attributes["status"] = 'completed'; // if all amount is paid, we update status to completed
        }
        $this->attributes["amount_paid"] = $val; //if there are formula for other field, we need to set it back
    }

    public function setSubTotalAttribute($val){
        $this->attributes["sub_total"] = $val;

        //To calculate grand total
        $disAmt = isset($this->discount_total) ? $this->discount_total : 0;
        $taxTotal = isset($this->tax_total) ? $this->tax_total : 0;

        $this->attributes["grand_total"] = $val + $taxTotal - $disAmt;
    }

    //Block Relationship
    public function billTo(){
        return $this->belongsTo('App\Model\PersonAccount', 'bill_to_id');
    }

    public function shipTo(){
        return $this->belongsTo('App\Model\Locations', 'ship_to_id')->with("parent");
    } 

    public function details(){
        return $this->hasMany('App\Model\InvoiceDetail', 'invoices_id', 'id')->with(["item", "product"]);
        // return $this->hasMany('App\Model\InvoiceDetail', 'invoices_id', 'id')->with("item")->orderBy("service_date", "asc");
    }

    public function invoiceReceipts(){
        return $this->hasMany('App\Model\InvoiceReceipt', 'invoices_id', 'id')->with("receipt");
    } 

    public function langs(){
        return $this->hasMany('App\Model\InvoiceTranslation', 'invoices_id', 'id');
    }

    public function shipments(){
        return $this->hasMany('App\Model\Shipment', 'invoices_id', 'id')->with('details');
    }
}
