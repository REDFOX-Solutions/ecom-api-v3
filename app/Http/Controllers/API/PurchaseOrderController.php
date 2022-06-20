<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PurchaseOrder;
use App\Http\Resources\RestResource;
use App\Services\DatabaseGW;
use App\Services\PurchaseOrderHandler;

class PurchaseOrderController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "purchase_orders",
            "model" => "App\Model\PurchaseOrder",
            'modelTranslate' => 'App\Model\PurchaseOrderTranslation',
            'prefixId' => 'po',
            'prefixLangId' => 'po0t',
            'parent_id' => 'purchase_orders_id'
        ];
    }

    public function getQuery(){
        return PurchaseOrder::query();
    }

    public function getModel(){
        return "App\Model\PurchaseOrder";
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


    public function beforeCreate(&$listNewRecords)
    {
        //to populate field that user didn't input or not required manual input
        PurchaseOrderHandler::setDefaultFieldsValue($listNewRecords);
    }
    
    public function afterCreate(&$lstNewRecords){
        
        PurchaseOrderHandler::CreatePurchaseDetail($lstNewRecords);
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
