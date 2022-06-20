<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Model\GeneralLedgerDetails;

class GeneralLedgerDetailsController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'general_ledger_details',
            'model' => 'App\Model\GeneralLedgerDetails', 
            'prefixId' => 'gl0d'
        ];
    }
    
    public function getQuery(){
        return GeneralLedgerDetails::query();
    }
    
    public function getModel(){
        return 'App\Model\GeneralLedgerDetails';
    } 
}
