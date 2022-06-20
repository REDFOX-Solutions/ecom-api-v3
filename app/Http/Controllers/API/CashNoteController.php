<?php

namespace App\Http\Controllers\API;
 
use App\Model\CashNote;
use App\Services\CashNoteHandler;
use Illuminate\Http\Request;

class CashNoteController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'cash_note',
            'model' => 'App\Model\CashNote', 
            'prefixId' => 'c0n'
        ];
    }
    
    public function getQuery(){
        return CashNote::query();
    }
    
    public function getModel(){
        return 'App\Model\CashNote';
    } 

    public function afterCreate(&$lstNewRecords){

        //do create all children record attached with cash note
        CashNoteHandler::createChildren($lstNewRecords);
    } 
}
