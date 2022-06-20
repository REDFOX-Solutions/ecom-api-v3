<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\GlobalPicklist;

class GlobalPicklistController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'global_picklist',
            'model' => 'App\Model\GlobalPicklist', 
            'prefixId' => 'glPick'
        ];
    }

    public function getQuery(){
        return GlobalPicklist::query();
    }
    
    public function getModel(){
        return 'App\Model\GlobalPicklist';
    }
}
