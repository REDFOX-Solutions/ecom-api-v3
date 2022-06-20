<?php

namespace App\Http\Controllers\API;

use App\Model\Pricebook;

class PricebookController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'pricebook',
            'model' => 'App\Model\Pricebook', 
            'prefixId' => 'pb'
        ];
    }
    
    public function getQuery(){
        return Pricebook::query();
    }
    
    public function getModel(){
        return 'App\Model\Pricebook';
    }   
    
}
