<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\TransferProductDetail;

class TransferProductDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "transfer_product_detail",
            "model" => "App\Model\TransferProductDetail",
            'modelTranslate' => 'App\Model\TransferProdDetailTranslate',
            'prefixId' => 'tpd',
            'prefixLangId' => 'tpd0t',
            'parent_id' => 'transfer_product_id'
        ];
    }

    public function getQuery(){
        return TransferProductDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\TransferProductDetail';
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
