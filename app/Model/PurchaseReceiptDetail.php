<?php

namespace App\Model;


class PurchaseReceiptDetail extends MainModel
{
    protected $table = 'pr_details';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    // protected $with = ['purchaseOrder'];
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
        "purchase_receipts_id",
        "product_id",
        "receive_qty",
        "po_detail_id",
        "orig_pr_detail_id",
        "purchase_order_id",
        "warehouse_id",
        "location_id",
        "uom_id",
        "prev_receive_qty",
        "prev_open_qty",
        "discount_rate",
        "discount_amount",
        "orig_purchase_receipts_id",
        "accrual_coa_id", //chart of account
        "inventory_coa_id"//chart of account

    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "receive_qty" => "double",
        "return_qty" => "integer",
        "prev_receive_qty" => "double",
        "prev_open_qty" => "double",
        "discount_rate" => "double",
        "discount_amount" => "double"
    ];

    protected $appends = ['return_qty'];
    public function getReturnQtyAttribute(){
        $podID = $this->po_detail_id;
        $prID = $this->id;
        $lst_return = PurchaseReceiptDetail::where("orig_pr_detail_id",$prID)
                                                ->get()->toArray();

        $total_return_qty = 0;
        foreach ($lst_return as $key => $value) {
            $total_return_qty += $value["receive_qty"];
        }
        return  $total_return_qty;

    }

    public function purchaseOrder(){
        return $this->belongsTo('App\Model\PurchaseOrder', 'purchase_order_id', 'id')->with("purchase_by");
    }

    public function origPrDetail(){
        return $this->belongsTo('App\Model\PurchaseReceiptDetail', 'orig_pr_detail_id', 'id')->with(["receipt","purchaseOrder"]);
    }

    public function purchaseOrderDetail(){
        return $this->belongsTo('App\Model\PurchaseOrderDetail', 'po_detail_id')->with("product");
    }

    public function orderDetails(){
        return $this->belongsTo('App\Model\PurchaseOrderDetail', 'po_detail_id')->with(["purchaseOrder","product"]);
    }
    public function receipt(){
        return $this->belongsTo('App\Model\PurchaseReceipt', 'purchase_receipts_id')->with("receiptBy");
    }
    public function receiptTobill(){
        return $this->belongsTo('App\Model\PurchaseReceipt', 'purchase_receipts_id')->with("bill");
    }
    public function receiptToBillPayments(){
        return $this->belongsTo('App\Model\PurchaseReceipt', 'purchase_receipts_id')->with("billToPayment");
    }

    public function product(){
        return $this->belongsTo('App\Model\Products', 'product_id');
    }
}
