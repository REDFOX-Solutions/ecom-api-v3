<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\ProductWarehouseDetail;

class ProdWareHouseDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'product_warehouse_details',
            'model' => 'App\Model\ProductWarehouseDetail', 
            'prefixId' => 'whD',
        ];
    }

    public function getQuery(){
        return ProductWarehouseDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\ProductWarehouseDetail';
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
