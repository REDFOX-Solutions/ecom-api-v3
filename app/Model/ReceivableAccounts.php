<?php

namespace App\Model;

use App\Services\DatetimeUtils;
use Carbon\Carbon;


class ReceivableAccounts extends MainModel
{
    protected $table = "receivable_accounts";
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'id',
        "created_by_id",
        "updated_by_id",
        "created_date",
        "updated_date",
        "is_backup",
        "ref_number", 
        "ar_type",
        "customer_id",
        "status",
        "ar_date",
        "due_date",
        "credit_term_id",
        "description",
        "gl_id"

    ];
    protected $casts = [
        "id" => "string",
        "is_backup"=>"integer"

    ];
    public  $appends=["total_amount","fin_period"];
    public function getTotalAmountAttribute(){
        $lstReceivable=0;
      $totalRe=  ReceivableAccountsDetails::where("receivable_account_id","{$this->id}")
        ->get()->toArray();
        foreach ($totalRe as $key => $value) {
            # code...
            $lstReceivable += isset($value["amount"]) ? $value["amount"] : 0;

        }
        return $lstReceivable;

    }
    public function customers(){
        return $this->belongsTo('App\Model\PersonAccount','customer_id');
    }
    public function receivableDetail(){
        return $this->hasMany('App\Model\ReceivableAccountsDetails','receivable_account_id','id');
    }
    
public function getFinPeriodAttribute(){
    return isset($this->transaction_date) ? DatetimeUtils::setPeriod($this->transaction_date) : null;

} 
}
