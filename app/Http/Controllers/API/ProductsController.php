<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Model\Products;  
use App\Services\DatabaseGW; 
use App\Http\Resources\RestResource;
use App\Services\GLAccMappingHandler;
use App\Services\ProductHandler;

class ProductsController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'products',
            'model' => 'App\Model\Products',
            'modelTranslate' => 'App\Model\ProductTranslation',
            'prefixId' => 'pro',
            'prefixLangId' => 'pro0t',
            'parent_id' => 'products_id'
        ];
    }
    
    public function getQuery(){
        return Products::query();
    }
    
    public function getModel(){
        return 'App\Model\Products';
    }   
    
    public function beforeCreate(&$lstNewRecords){
        ProductHandler::setupDefaultFieldOnCreate($lstNewRecords);
        # code logic here ...
    }
    
    public function afterCreate(&$lstNewRecords){

        //create default pricebook entry for that product
        ProductHandler::createDefaultPbe($lstNewRecords);

        //create default costing
        // ProductHandler::createDefaultCosting($lstNewRecords); 
        
        
    }
    
    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){
        //update default pricebook entry price for that product
        ProductHandler::createDefaultPbe($lstNewRecords);

        foreach ($lstNewRecords as $index => $newProduct) {
            $oldProduct = $mapOldRecords[$newProduct["id"]];

            //to update costing
            // ProductHandler::createCostingOnUpdate($oldProduct, $newProduct);
        }
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords=[]){ 
        // update product option, if master is updated
        ProductHandler::updateProductOptions($lstNewRecords, $mapOldRecords);
    }
 
     
    
    public function publicProduct(Request $req){
        try{
            // return [];
            $lstFilter = $req->all(); 
            $lstFilter['is_active'] = 1;
            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $lstFilter));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }
   
}
