<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PurchaseOrderDetail;
use App\Services\PurchaseOrderHandler;
use App\Http\Resources\RestResource;
use App\Services\DatabaseGW;

class PurchaseOrderDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "po_details",
            "model" => "App\Model\PurchaseOrderDetail",
            'modelTranslate' => 'App\Model\PurchaseOrderDetailTranslation',
            'prefixId' => 'pod',
            'prefixLangId' => 'podt',
            'parent_id' => 'po_details_id'
        ];
    }

    public function getQuery(){
        return PurchaseOrderDetail::query();
    }

    public function getModel(){
        return "App\Model\PurchaseOrderDetail";
    }
    public function getCreateRules(){
        return [
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }

    public function afterCreate(&$lstNewRecords){       

        foreach ($lstNewRecords as $key => &$newPODetail) {
            //Recalculate order
            PurchaseOrderHandler::reCalcOrder($newPODetail["purchase_orders_id"]);
        } 
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords = []){

        foreach ($lstNewRecords as $key => &$newPODetail) {
            //Recalculate order
            PurchaseOrderHandler::reCalcOrder($newPODetail["purchase_orders_id"]);
        } 

    }
    public function afterDelete($lstOldRecords){
        
        foreach ($lstOldRecords as $key => &$newPODetail) {
            //Recalculate order
            PurchaseOrderHandler::reCalcOrder($newPODetail["purchase_orders_id"]);
        } 
    }

    //guest access
    public function publicIndex(Request $request){
        try{
            $lstFilter = $request->all(); 
            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $lstFilter));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }
}
