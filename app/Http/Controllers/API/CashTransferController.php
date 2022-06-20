<?php

namespace App\Http\Controllers\API;

use App\Model\CashTransfer;
use App\Services\GLHandler;
use Illuminate\Http\Request;
use App\Services\CashTransferHandler;
use App\Model\GeneralLedger;
use App\Services\JournalEntryHandler;

class CashTransferController extends RestAPI
{
    //
    protected function getQuery()
    {
        return CashTransfer::query();
    }

    protected function getModel()
    {
        return 'App\Model\CashTransfer';
    }

    protected function getTableSetting()
    {
        return [
            'tablename' => 'cash_transfer',
            'model' => 'App\Model\CashTransfer',
            'prefixId' => 'ctf'
        ];
    }
    public function beforeCreate(&$lstNewRecord)
    {
        CashTransferHandler::setDefaultValue($lstNewRecord);
    }
    // after created CA to create CA Detail
    public function afterCreate(&$lstNewRecords)
    { 
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [])
    {

        foreach ($lstNewRecords as $key => $cash) { 

            //if cash transfer released, we will create Journal Entry for it
            if (isset($cash["status"]) && $cash["status"] == "released" && $cash["is_void"]==0) { 
                JournalEntryHandler::createJEFromCT($cash["id"], false);
            }
             //if cash transfer released, we will create Journal Entry for it
             if (isset($cash["status"]) && $cash["status"] == "released" && $cash["is_void"]==1) { 
                JournalEntryHandler::createJEFromCT($cash["id"], true);
            }
            // if  status released we will copy new obj
            if (isset($cash["status"]) && $cash["status"] == "voided") { 
                CashTransferHandler::isVoid($cash["id"]);
            }
            # code...
            if (isset($cash["transfer_type"]) && $cash["transfer_type"] != "cash entry") {
                CashTransferHandler::deleteCA($cash['id']);
            }
        }
    }
    public function  afterDelete($lstOldRecords)
    {
        CashTransferHandler::deleteCADetail($lstOldRecords);
    }
}
