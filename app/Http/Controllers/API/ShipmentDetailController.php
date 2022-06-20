<?php

namespace App\Http\Controllers\API;

use App\Model\ShipmentDetail;

class ShipmentDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'shipment_details',
            'model' => 'App\Model\ShipmentDetail', 
            'prefixId' => 'shipD'
        ];
    }
    
    public function getQuery(){
        return ShipmentDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\ShipmentDetail';
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
    
}
