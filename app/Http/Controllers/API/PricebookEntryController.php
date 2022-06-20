<?php

namespace App\Http\Controllers\API;
 
use App\Model\PricebookEntry;

class PricebookEntryController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'pricebook_entry',
            'model' => 'App\Model\PricebookEntry', 
            'prefixId' => 'pbe'
        ];
    }
    
    public function getQuery(){
        return PricebookEntry::query();
    }
    
    public function getModel(){
        return 'App\Model\PricebookEntry';
    }   
    
    public function beforeCreate(&$lstNewRecords){
        # code logic here ...
    }
    
    public function afterCreate(&$lstNewRecords){
        // $this->createProductCategory($lstNewRecords);
    }
    
    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){
        // $this->createProductCategory($lstNewRecords);
    } 
}
