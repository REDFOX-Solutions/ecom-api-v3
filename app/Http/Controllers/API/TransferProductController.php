<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\TransferProduct;

class TransferProductController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "transfer_product",
            "model" => "App\Model\TransferProduct",
            'modelTranslate' => 'App\Model\TransferProdTranslate',
            'prefixId' => 'tp',
            'prefixLangId' => 'tp0t',
            'parent_id' => 'transfer_product_id'
        ];
    }

    public function getQuery(){
        return TransferProduct::query();
    }
    
    public function getModel(){
        return 'App\Model\TransferProduct';
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
