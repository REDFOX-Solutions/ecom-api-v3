<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LoginHistory;

class LoginHistoryController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'login_histories',
            'model' => 'App\Model\LoginHistory', 
            'prefixId' => 'logHis'
        ];
    }
    
    public function getQuery(){
        return LoginHistory::query();
    }
    
    public function getModel(){
        return 'App\Model\LoginHistory';
    } 
    
}
