<?php

namespace App\Http\Controllers\API;
 
use App\Model\Baskets;
use Illuminate\Http\Request;

class BasketController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'baskets',
            'model' => 'App\Model\Baskets', 
            'prefixId' => 'bk'
        ];
    }
    
    public function getQuery(){
        return Baskets::query();
    }
    
    public function getModel(){
        return 'App\Model\Baskets';
    } 
}
