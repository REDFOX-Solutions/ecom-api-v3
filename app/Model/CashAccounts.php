<?php

namespace App\Model;


use App\Exceptions\CustomException;
class CashAccounts extends MainModel
{
    protected $table = "cash_accounts";
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
        "payment_method_id",
        "name",
        "currency_id",
        "chart_of_acc_id",
        "is_active",
        "is_receive_acc",
        "accept_currency_ids",
        "company_id",
        "pos_usable",
        "ordering",
        "icon",
        "image"

    ];
    protected $casts = [
        "id" => "string",
        "is_active"=>"integer",
        "is_receive_acc"=>"integer",
        "is_backup"=>"integer",
        "pos_usable" => "integer",
        "ordering" => "integer"
    ];

    public function paymentMethod(){
        return $this->belongsTo('App\Model\PaymentMethod', 'payment_method_id');
    }
    public function currency(){
        return $this->belongsTo('App\Model\ShopCurrency', 'currency_id');
    }

    public function chartofaccount(){
        return $this->belongsTo('App\Model\ChartOfAccount', 'chart_of_acc_id');
    }

    public $appends=['accept_currencies_r'];

    public function getAcceptCurrenciesRAttribute(){
        
        $acceptIds="{$this->accept_currency_ids}";
        if(isset($acceptIds) && !empty($acceptIds)){
            //   get value is string
            // use explode for convert to array
            // $lstCurrencyIds = explode(",",$acceptIds);
            $lstCurrencyIds = explode(",",$acceptIds);
            // throw new CustomException("Output Cash Account!", $acceptIds);
            return ShopCurrency::whereIn("id", $lstCurrencyIds)
                                    ->get()
                                    ->toArray();
        }
        return []; 
        //   $models = Model::whereIn('id', [1, 2, 3])->get();

    }



}