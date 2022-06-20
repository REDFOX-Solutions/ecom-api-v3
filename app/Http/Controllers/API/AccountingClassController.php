<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\AccountingClass;

class AccountingClassController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'accounting_class',
            'model' => 'App\Model\AccountingClass', 
            'prefixId' => 'acc0cls'
        ];
    }
    
    public function getQuery(){
        return AccountingClass::query();
    }
    
    public function getModel(){
        return 'App\Model\AccountingClass';
    } 

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){ 
        
        foreach ($lstNewRecords as $index => &$accClass) {

            //chart of account not allow to update field code and accounting class
            if(isset($accClass["name"])){
                unset($accClass["name"]);
            }

            if(isset($accClass["class_type"])){
                unset($accClass["class_type"]);
            }
        }
        
    }
}
