<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\CurrencyExchangeRate;
use App\Services\CurrencyExchangeRateHandler;
use Illuminate\Http\Request;

class CurrencyExchangeRateController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "currency_exchange_rate",
            "model" => "App\Model\CurrencyExchangeRate", 
            "prefixId" => "exc"
        ];
    }

    public function getQuery(){
        return CurrencyExchangeRate::query();
    }

    public function getModel(){
        return "App\Model\CurrencyExchangeRate";
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

        //we will create history record if rate has change
        CurrencyExchangeRateHandler::createHistory($mapOldRecords, $lstNewRecords);
    }
}
