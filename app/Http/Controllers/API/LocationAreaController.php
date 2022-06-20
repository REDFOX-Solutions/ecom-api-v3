<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LocationArea;

class LocationAreaController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'location_area',
            'model' => 'App\Model\LocationArea', 
            'prefixId' => 'area'
        ];
    }
    
    public function getQuery(){
        return LocationArea::query();
    }
    
    public function getModel(){
        return 'App\Model\LocationArea';
    } 
}
