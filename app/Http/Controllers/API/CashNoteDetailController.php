<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CashNoteDetails;

class CashNoteDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'cash_note_details',
            'model' => 'App\Model\CashNoteDetails', 
            'prefixId' => 'c0n0d'
        ];
    }
    
    public function getQuery(){
        return CashNoteDetails::query();
    }
    
    public function getModel(){
        return 'App\Model\CashNoteDetails';
    }
}
