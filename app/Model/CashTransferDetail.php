<?php

namespace App\Model;



class CashTransferDetail extends MainModel
{
    //
    protected $table = "cash_transfer_details";
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
        'product_id',
        'cash_acc_id',
        'product_coa_id',
        'cash_acc_coa_id',
        'description',
        'amount',
        'cash_transfer_id',
        'detail_type',
       
        ];
        protected $casts = [
            "id" => "string",
            "is_backup" => "integer",
            
            "amount" => "double",
        ];
        public function products(){
            return $this->belongsTo('App\Model\Products','product_id')->with('chartOfAccount');
        }
        public function cashAccount(){
            return $this->belongsTo('App\Model\CashAccounts','cash_acc_id');
        }
        public function chartOfAccount(){
            return $this->belongsTo("App\Model\ChartOfAccount","product_coa_id");
        }
}
