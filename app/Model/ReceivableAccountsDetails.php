<?php

namespace App\Model;



class ReceivableAccountsDetails extends MainModel
{
    protected $table = "receivable_account_details";
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
        "receivable_account_id",
        "qty",
        "cost",
        "amount",
        "description",
        "product_id"

    ];
    protected $casts = [
        "id" => "string",
        "is_backup"=>"integer",
        "cost"=>"double",
        "qty"=>"double",
        "amount"=>"double"

    ];
    public function products(){
        return $this->belongsTo('App\Model\Products','product_id');
    }
}
