<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductGLAccount extends Model
{
    protected $table = 'product_glaccount';
    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name
    protected $fillable = [
    "id",
    "created_by_id",
    "updated_by_id",
    "created_date",
    "updated_date",
    "is_backup",
    "inventory_acc_id",
    "inv_sub",
    "reason_code_sub",
    "sales_acc_id",
    "sales_sub",
    "cogs_acc_id",
    "cogs_sub",
    "std_cost_variance_acc_id",
    "std_cost_variance_sub",
    "std_cost_revaluation_acc_id",
    "std_cost_revaluation_sub",
    "po_accrual_acc_id",
    "po_accrual_sub",
    "purchase_price_var_acc_id",
    "purchase_price_var_sub",
    "landed_cost_var_acc_id",
    "landed_cost_var_sub_id",
    "deferral_acc_id",
    "deferral_sub",
    "products_id"
    ];

    protected $casts = [];

    public function productGlaccount(){
        return $this->belongsTo('App\Model\Products','id');
    }
}
