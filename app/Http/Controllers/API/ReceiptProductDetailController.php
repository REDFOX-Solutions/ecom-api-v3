<?php

namespace App\Http\Controllers\API;
 
use App\Model\ReceiptProductDetail;
use Illuminate\Http\Request;

class ReceiptProductDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'receipt_product_detail',
            'model' => 'App\Model\ReceiptProductDetail', 
            'prefixId' => '810',
            'modelTranslate' => 'App\Model\ReceiptProductDetailTranslate', 
            'prefixLangId' => '810T',
            'parent_id' => 'receipt_product_detail_id'
        ];
    }

    public function getQuery(){
        return ReceiptProductDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\ReceiptProductDetail';
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
