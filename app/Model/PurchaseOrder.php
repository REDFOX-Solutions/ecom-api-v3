<?php

namespace App\Model;


use App\Model\PurchaseReceiptDetail;
use App\Model\PurchaseReceipt;
use App\Exceptions\CustomException;


class PurchaseOrder extends MainModel
{
    protected $table = 'purchase_orders';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ["langs"];
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
        "status",
        "total_qty",  
        "total_cost",
        "po_date",
        "promised_date",
        "vendor_id",
        "total_discount",
        "vat_exempt_total", 
        "vat_taxable_total",
        "tax_total",
        "po_num",
        "po_type",
        "purchase_by_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "total_qty" => "double",
        "total_cost" => "double",
        "total_discount" => "double",
        "vat_exempt_total" => "double",
        "vat_taxable_total" => "double",
        "tax_total" => "double",
        "count_receipt" => "integer"
    ];

    protected $appends = ['count_receipt', 'count_return', 'count_bill', 'count_payment'];

    //** count length receipts */
    public function getCountReceiptAttribute(){
        $poId = $this->id;
        $total_count = PurchaseReceiptDetail::where("purchase_order_id", $poId)
                        ->where("orig_pr_detail_id", null)->get()
                        ->groupBy("purchase_receipts_id")
                        ->count();
        return  $total_count;

    }
    //** count length receipts return */
    public function getCountReturnAttribute(){
        $poId = $this->id;
        $total_count = PurchaseReceiptDetail::where("purchase_order_id", $poId)
                        ->where("orig_pr_detail_id","<>",null)->get()
                        ->groupBy("purchase_receipts_id")
                        ->count();
        return  $total_count;

    }

    //** count length bills */
    public function getCountBillAttribute(){

        $poId = $this->id;
        $lstReceiptDetail = PurchaseReceiptDetail::where("purchase_order_id", $poId)->with("receiptTobill")->get()->toArray();
        
        $billId = [];
        foreach ($lstReceiptDetail as $key => $receiptDetail) {

            if (isset($receiptDetail["receipt_tobill"]["purchase_bills_id"]) && !empty($receiptDetail["receipt_tobill"]["purchase_bills_id"])) {
                
                $subobjBill [$key]= $receiptDetail["receipt_tobill"]["purchase_bills_id"];
                $billId = $subobjBill; 
            }
        }
        $result = array_unique($billId);
        return  count($result);
    }
    //** count length payments */
    public function getCountPaymentAttribute(){

        $poId = $this->id;
        $lstReceiptDetail = PurchaseReceiptDetail::where("purchase_order_id", $poId)->with("receiptToBillPayments")->get()->toArray();
        
        $billId = [];
        foreach ($lstReceiptDetail as $key => $receiptDetail) {

            if (isset($receiptDetail["receipt_to_bill_payments"]) && !empty($receiptDetail["receipt_to_bill_payments"])) {
                
                if (isset($receiptDetail["receipt_to_bill_payments"]["bill_to_payment"]) && !empty($receiptDetail["receipt_to_bill_payments"]["bill_to_payment"])) {
                
                    if (isset($receiptDetail["receipt_to_bill_payments"]["bill_to_payment"]["bill_payments"]) && !empty($receiptDetail["receipt_to_bill_payments"]["bill_to_payment"]["bill_payments"])) {
                
                        $subobjBill [$key]= $receiptDetail["receipt_to_bill_payments"]["bill_to_payment"]["bill_payments"]["purchase_payments_id"];
                        $billId = $subobjBill; 
                    }
                }
            }
        }
        $result = array_unique($billId);
        return  count($result);
    }



    public function langs(){
        return $this->hasMany('App\Model\PurchaseOrderTranslation', 'purchase_orders_id', 'id');
    }
    public function vendor(){
        return $this->belongsTo('App\Model\PersonAccount', 'vendor_id', 'id');
    }
    public function orderDetails(){
        return $this->hasMany('App\Model\PurchaseOrderDetail', 'purchase_orders_id', 'id')->with(["product", "receiptDetail"]);
    }
    public function purchase_by(){
        return $this->belongsTo('App\Model\User', 'purchase_by_id');
    }
    
}
