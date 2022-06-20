<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ProductStandardCost;

class ProductStandardCostController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'product_standard_cost',
            'model' => 'App\Model\ProductStandardCost', 
            'prefixId' => 'stdCst', 
        ];
    }
 
    
    public function getQuery(){
        return ProductStandardCost::query();
    }
    
    public function getModel(){
        return 'App\Model\ProductStandardCost';
    }   
    
    public function beforeCreate(&$lstNewRecords){
        # code logic here ...
    }
    
    public function afterCreate(&$lstNewRecords){ 
    }
    
    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){ 
    }
  
}
