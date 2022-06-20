<?php

namespace App\Http\Controllers\API;
 
use Illuminate\Http\Request; 
use App\Model\SaleOrder; 
use App\Services\FloorTableHandler;
use App\Services\RecordTypeHandler;
use App\Services\ReportHandler;
use App\Services\SaleOrderHandler; 
use App\Services\SalesOrderDetailHandler;

class SaleOrderController extends RestAPI
{
    

    public function getTableSetting(){
        return [
            'tablename' => 'sales_order',
            'model' => 'App\Model\SaleOrder',
            'modelTranslate' => 'App\Model\SaleOrderTranslation',
            'prefixId' => 'SO', 
            'prefixLangId' => 'SO0t',
            'parent_id' => 'sales_order_id'
        ];
    }
    
    public function getQuery(){
        return SaleOrder::query();
    }
    
    public function getModel(){
        return 'App\Model\SaleOrder';
    }
    
    public function getCreateRules(){
        return [  ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }

    public function beforeCreate(&$lstNewRecords){

        //to populate field that user didn't input or not required manual input
        SaleOrderHandler::setDefaultFieldsValue($lstNewRecords); 
    }
    
    public function afterCreate(&$lstNewRecords){ 

    }
    
    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){ 
         
    } 
 
    public function afterUpdate(&$lstNewRecords, $mapOldRecords=[]){
    }

    public function afterDelete($lstOldRecords){ 
 
        
    }

    public function publicStore(Request $req){
        $this->noAuth = true;
        return $this->store($req);
    }
 
 
}
