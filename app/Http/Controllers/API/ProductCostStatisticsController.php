<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ProductCostStatistics; 
class ProductCostStatisticsController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'product_coststatistics',
            'model' => 'App\Model\ProductCostStatistics',
            'prefixId' => 'procosta',
        ];
    }

    public function getQuery(){
        return ProductCostStatistics::query();
    }

    public function getModel(){
        return "App\Model\ProductCostStatistics";
    }
}
