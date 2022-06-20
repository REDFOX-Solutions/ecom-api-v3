<?php

namespace App\Model;

use App\Http\Controllers\API\RestAPI;
use App\Services\DatetimeUtils; 

class CashTransfer extends MainModel
{
    //
    protected $table = "cash_transfer";
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
    'id',
    'created_by_id',
    'updated_by_id',
    'created_date',
    'updated_date',
    'is_backup',
    'ref_num',
    'transaction_ref_num',
    'transfer_date',
    'is_void',
    'transfer_type',
    'from_cash_acc_id',
    'to_cash_acc_id',
    'transfer_amount',
    'description',
    'status',
    'charge_amount',
    'total_amount',
    'note',
    "recordtype_name",
    "recordtype_id"
    ];
    protected $appends = ['fin_period'];

    protected $casts = [
        "id" => "string",
        "is_backup" => "integer",
        "is_void" => "integer",
        "charge_amount" => "double",
        "total_amount" => "double",
        "updated_by_id" => "string"
    ];

    public static $relationship = [
        "children" => [ 
            [
                "name" => "cash_transfer_details", 
                "parent_field" => "cash_transfer_id",
                "controller" => "App\Http\Controllers\API\CashTransferDetailController"
            ],
           
            
        ],
        "parent" => []
    ];

    public function fromCashAccount(){
        return $this->belongsTo('App\Model\CashAccounts', 'from_cash_acc_id');
    }
    public function toCashAccount(){
        return $this->belongsTo('App\Model\CashAccounts', 'to_cash_acc_id');
    }
  
    public function getFinPeriodAttribute(){
        return isset($this->transfer_date) ? DatetimeUtils::setPeriod($this->transfer_date) : null;
    }

    public function generalLedger(){
        return $this->hasMany('App\Model\GeneralLedger', 'cash_transfer_id','id');
    }
    public function cashTransferDetails(){
        return $this->hasMany('App\Model\CashTransferDetail','cash_transfer_id','id');
    }
    public function receipts(){
        return $this->hasMany('App\Model\CashTransferDetail','cash_transfer_id','id')->where("detail_type", "receipt");
    }
    public function desbursements(){
        return $this->hasMany('App\Model\CashTransferDetail','cash_transfer_id','id')->where("detail_type", "disbursement");
    }
   
}
