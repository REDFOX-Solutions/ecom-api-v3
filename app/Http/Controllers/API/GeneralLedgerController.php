<?php

namespace App\Http\Controllers\API;

use App\Model\GeneralLedger;
use Illuminate\Http\Request; 

class GeneralLedgerController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'general_ledger',
            'model' => 'App\Model\GeneralLedger', 
            'prefixId' => 'gl'
        ];
    }
    
    public function getQuery(){
        return GeneralLedger::query();
    }
    
    public function getModel(){
        return 'App\Model\GeneralLedger';
    } 
    public function beforeCreate(&$lstNewRecords)
    {
        JournalEntryHandler::setDefaultValue($lstNewRecords);
    }
    public function afterCreate(&$lstNewRecords)
    {

        JournalEntryHandler::onReversed($lstNewRecords); 
    }

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
    }
    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [])
    {

        foreach ($lstNewRecords as $key => $newGL) {
            $oldGL = $mapOldRecords[$newGL["id"]];

            if (
                isset($newGL["status"]) &&
                $newGL["status"] != $oldGL["status"] &&
                $newGL["status"] == "posted"
            ) {
                //log COA transaction after update to posted
                COATransactionHandler::logCOAFromGL($newGL);
            }
        }
    }
    public function beforeDelete(&$lstOldRecords)
    {
        foreach ($lstOldRecords as $key => $gl) {
            # code...
            JournalEntryHandler::deleteGlDetail($gl["id"]);
        }
    }
}
