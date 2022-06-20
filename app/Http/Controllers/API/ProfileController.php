<?php

namespace App\Http\Controllers\API;
 
use App\Model\Profile;

class ProfileController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'profiles',
            'model' => 'App\Model\Profile', 
            'prefixId' => '00e'
        ];
    }
    
    public function getQuery(){
        return Profile::query();
    }
    
    public function getModel(){
        return 'App\Model\Profile';
    }   
    
}
