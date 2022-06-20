<?php

namespace App\Http\Controllers\API;

use App\Model\ChartOfAccount;
use Illuminate\Http\Request; 

class ChartOfAccountController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'chart_of_account',
            'model' => 'App\Model\ChartOfAccount', 
            'prefixId' => 'coa'
        ];
    }
    
    public function getQuery(){
        return ChartOfAccount::query();
    }
    
    public function getModel(){
        return 'App\Model\ChartOfAccount';
    } 

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){ 
        
        foreach ($lstNewRecords as $index => &$coa) {

            //chart of account not allow to update field code and accounting class
            if(isset($coa["code"])){
                unset($coa["code"]);
            }

            if(isset($coa["accounting_class_id"])){
                unset($coa["accounting_class_id"]);
            }
        }
        
    }
}
