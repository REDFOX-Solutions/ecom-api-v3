<?php

namespace App\Http\Controllers\API;

use App\Model\CurrencySheets;

class CurrencySheetsController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'currency_sheets',
            'model' => 'App\Model\CurrencySheets', 
            'prefixId' => 'c0s'
        ];
    }
    
    public function getQuery(){
        return CurrencySheets::query();
    }
    
    public function getModel(){
        return 'App\Model\CurrencySheets';
    } 
}
