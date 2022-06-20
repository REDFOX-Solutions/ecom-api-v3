<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\CurrencyExchangeRateHis;
use Illuminate\Http\Request;

class CurrencyExchangeRateHisController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "currency_exchange_rate_his",
            "model" => "App\Model\CurrencyExchangeRateHis", 
            "prefixId" => "excHis"
        ];
    }

    public function getQuery(){
        return CurrencyExchangeRateHis::query();
    }

    public function getModel(){
        return "App\Model\CurrencyExchangeRateHis";
    }
    
    public function getCreateRules(){
        return [];
    }

    public function getUpdateRules(){
        return [];
    }

    public function beforeCreate(&$lstNewRecords){
        # code logic here ...
    }
 
    public function afterCreate(&$lstNewRecords){
        # code logic here ...
    }

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){
        # code logic here ...
    }
 
    public function afterUpdate(&$lstNewRecords, $mapOldRecords=[]){
        # code logic here ...
    }
}
