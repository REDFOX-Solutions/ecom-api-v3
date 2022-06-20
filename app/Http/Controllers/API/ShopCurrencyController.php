<?php

namespace App\Http\Controllers\API;

use App\Model\ShopCurrency;

class ShopCurrencyController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'shop_currency',
            'model' => 'App\Model\ShopCurrency', 
            'prefixId' => 's0cur'
        ];
    }
    
    public function getQuery(){
        return ShopCurrency::query();
    }
    
    public function getModel(){
        return 'App\Model\ShopCurrency';
    } 
}
