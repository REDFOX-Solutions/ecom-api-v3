<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ProductGLAccount;
class ProductGLAccountController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'product_glaccount',
            'model' => 'App\Model\ProductGLAccount',
            'prefixId' => 'proglaccount'
        ];
    }

    public function getQuery(){
        return ProductGLAccount::query();
    }
    
    public function getModel(){
        return "App\Model\ProductGLAccount";
    }
}
