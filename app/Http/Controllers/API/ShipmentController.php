<?php

namespace App\Http\Controllers\API;

use App\Model\Shipment;
use App\Services\ShipmentHandler;

class ShipmentController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'shipments',
            'model' => 'App\Model\Shipment', 
            'prefixId' => 'ship'
        ];
    }

    public function getQuery(){
        return Shipment::query();
    }
    
    public function getModel(){
        return 'App\Model\Shipment';
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

    public function beforeCreate(&$lstNewRecords){
        
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
        
    }
}
