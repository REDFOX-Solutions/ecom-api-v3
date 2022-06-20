<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\SaleOrderDetail;
use App\Services\SaleOrderHandler;
use App\Services\SalesOrderDetailHandler;

class SaleOrderDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'sale_order_details',
            'model' => 'App\Model\SaleOrderDetail',
            'modelTranslate' => 'App\Model\SaleOrderDetailTranslation',
            'prefixId' => 'ordDe',
            'prefixLangId' => 'ordDe0t',
            'parent_id' => 'sale_order_details_id'
        ];
    }
    
    public function getQuery(){
        return SaleOrderDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\SaleOrderDetail';
    }
    
    public function getCreateRules(){
        return [
            'sales_order_id' => 'required',
            "products_id" => "required"
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }

    public function beforeCreate(&$lstNewRecords){
        //setup default value
        SalesOrderDetailHandler::setDefaultField($lstNewRecords);
    }
    
    public function afterCreate(&$lstNewRecords){       

        foreach ($lstNewRecords as $key => &$newSODetail) {
            //Recalculate order
            SaleOrderHandler::reCalcOrder($newSODetail["sales_order_id"]);
        }
    }
    
    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){
        // code logic here
    }
    
    public function afterUpdate(&$lstNewRecords, $mapOldRecords = []){

        foreach ($lstNewRecords as $key => &$newSODetail) {
            //Recalculate order
            SaleOrderHandler::reCalcOrder($newSODetail["sales_order_id"]);
        } 

    }

    public function afterDelete($lstOldRecords){
        
        foreach ($lstOldRecords as $key => &$newSODetail) {
            //Recalculate order
            SaleOrderHandler::reCalcOrder($newSODetail["sales_order_id"]);
        } 
    }

    public function publicStore(Request $req){
        $this->noAuth = true;
        return $this->store($req);
    }
}
