<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\FloorTable;

class FloorTableController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'floor_table',
            'model' => 'App\Model\FloorTable', 
            'prefixId' => 'tbl'
        ];
    }
    
    public function getQuery(){
        return FloorTable::query();
    }
    
    public function getModel(){
        return 'App\Model\FloorTable';
    }
    
    public function getCreateRules(){
        return [
           "table_name" => "required"
        ];
    }
}
