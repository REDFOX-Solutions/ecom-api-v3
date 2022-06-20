<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\CurrencyPicklist;
use Illuminate\Http\Request;

class CurrencyPicklistController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'currency_picklist',
            'model' => 'App\Model\CurrencyPicklist',
            'prefixId' => 'acur'
        ];
    }
    
    public function getQuery(){
        return CurrencyPicklist::query();
    }
    
    public function getModel(){
        return 'App\Model\AvailableCurrency';
    }
    
    public function getCreateRules(){
        return [
            'name' => 'required',
            'code' => 'required'
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }
}
