<?php

namespace App\Model;

use Carbon\Carbon;

use App\Model\ShipmentDetail;
use App\Model\SaleOrderDetail;

class SaleOrder extends MainModel
{
    protected $table = 'sales_order';
    protected $keyType = 'string';
    // protected $dateFormat = 'Y-m-d\TH:i:s.uP';
    public $incrementing = false;
    protected $with = ['langs', "printedInvoiceHistories"];
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
		"so_type",
		"order_num",
		"status",
		"order_date",
        "requested_on",
        "location_id",
		"customer_id",//person_account object
		"project_id",
		"ordered_qty",
		"discount_total",
		"vat_exempt_total",
		"vat_taxable_total",
		"tax_total",
		"order_total",
		"saler_id",
        "record_type_id",
        "sub_total",
        "table_no",
        "people",
        "checkout_date",
        "shipped_qty",
        "discount_rate",
        "floor_table_id",
        "is_end_shift",
        "staff_shifts_id",
        "printed_inv",
        "completed_by_id", //user object
        "transaction_date", // for business that has pass day
        "due_balance",
        "channel",
        "store_id",
        "prepayment_ids",
        "pricebook_id",
        "delivery_id"
       
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "ordered_qty" => "integer",
        "discount_total" => "double",
        "vat_exempt_total" => "double",
        "vat_taxable_total" => "double",
        "order_total" => "double",
        "tax_total" => "double",
        "sub_total" => "double",
        "people" => "integer",
        "is_end_shift" => "integer",
        "discount_rate" => "double",
        "printed_inv" => "integer",
        "shipped_qty" => "integer",
        "due_balance" => "double",
        "grand_total" => "double",
        "total_cost" => "double"
        
    ];

    protected $appends = ["duration", "grand_total", "shipment_count", "invoices_count", "total_cost"];

    public function getDurationAttribute(){
        $now = Carbon::now();
        $endTime = isset($this->checkout_date) && !empty($this->checkout_date) ? Carbon::parse($this->checkout_date): $now;
        $startTime = isset($this->order_date) && !empty($this->order_date)  ? Carbon::parse($this->order_date) : $now;

        $totalDuration = $endTime->diffInSeconds($startTime);
 
        $hour = gmdate('H', $totalDuration);
        $mn =  gmdate('i', $totalDuration);

        return ($hour == '00' ? '' : ($hour . 'h ')) . $mn . 'mn';
    }

    public function getGrandTotalAttribute(){ 
        $sub_total = isset($this->sub_total) ? $this->sub_total : 0;  
        $disPrice = isset($this->discount_amount) ? $this->discount_amount : 0;

        return $sub_total - $disPrice;
    }

    public function getShipmentCountAttribute() {
        $orderId = $this->id;
        $count = ShipmentDetail::where("sales_order_id", $orderId)->get()->groupBy("shipments_id")->count();
        return $count;
    }

    public function getTotalCostAttribute() {
        $orderId = $this->id;
        $cost = SaleOrderDetail::where("sales_order_id", $orderId)
                                ->get()
                                ->sum("unit_cost");
        return $cost;
    }

    public function getInvoicesCountAttribute() {
        return 0;
        // $orderId = $this->id;
        // $lstShipmentDetailRecords = ShipmentDetail::where("sales_order_id", $orderId)->get()->groupBy("shipments_id")->toArray();

        // $lstShipmentIds = [];
        // if (isset($lstShipmentDetailRecords) && !empty($lstShipmentDetailRecords)) {

        //     foreach ($lstShipmentDetailRecords as $index => $shipmentDetail) {
        //         $lstShipmentIds[] = $shipmentDetail;
        //     }
        //     return $lstShipmentIds;
        // }
    }

    public function langs(){
        return $this->hasMany('App\Model\SaleOrderTranslation', 'sales_order_id', 'id');
    }

    public function recordType(){
        return $this->belongsTo('App\Model\RecordType', 'record_type_id');
    }

    public function customer(){
        return $this->belongsTo('App\Model\PersonAccount', 'customer_id');
    }

    public function delivery(){
        return $this->belongsTo('App\Model\PersonAccount', 'delivery_id');
    }

    public function table(){
        return $this->belongsTo('App\Model\FloorTable', 'floor_table_id');
    }

    public function orderDetails(){
        return $this->hasMany('App\Model\SaleOrderDetail', 'sales_order_id', 'id')->with("product");
    }

    public function activeOrderDetails(){
        //TODO: we will have active order detail by status or other fields
        return $this->hasMany('App\Model\SaleOrderDetail', 'sales_order_id', 'id')
                    ->with("product")
                    ->orderBy("ordering", "asc");
    }

    public function printedInvoiceHistories(){
        return $this->hasMany('App\Model\PrintInvoiceHistory', 'sales_order_id', 'id');
    }

    public function shipmentDetail() {
        return $this->hasMany('App\Model\ShipmentDetail', 'sales_order_id', 'id');
    }

    public function pricebook(){
        return $this->belongsTo('App\Model\Pricebook', 'pricebook_id');
    }

    public function store(){
        return $this->belongsTo('App\Model\Company', 'store_id');
    }
}
