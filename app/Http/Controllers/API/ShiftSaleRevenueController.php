<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ShiftSaleRevenue;

class ShiftSaleRevenueController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'shift_sale_revenue',
            'model' => 'App\Model\ShiftSaleRevenue', 
            'prefixId' => 's0s0r'
        ];
    }
    
    public function getQuery(){
        return ShiftSaleRevenue::query();
    }
    
    public function getModel(){
        return 'App\Model\ShiftSaleRevenue';
    } 
    
}
