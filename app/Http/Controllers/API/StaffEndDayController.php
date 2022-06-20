<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\StaffEndDay;
use App\Services\AccountingHandler;
use App\Services\StaffEndDayHandler;
use App\Services\StaffShiftHandler;

class StaffEndDayController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'staff_end_day',
            'model' => 'App\Model\StaffEndDay', 
            'prefixId' => 's0e0d'
        ];
    }
    
    public function getQuery(){
        return StaffEndDay::query();
    }
    
    public function getModel(){
        return 'App\Model\StaffEndDay';
    }


    public function beforeCreate(&$lstNewRecords){
        
        StaffEndDayHandler::setDefaultFieldsValue($lstNewRecords); 
    }

    public function afterCreate(&$lstNewRecords){ 
        // StaffEndDayHandler::updateStaffShift($lstNewRecords);

        foreach ($lstNewRecords as $index => $record) {

            //update staff shift record 
            StaffShiftHandler::endDayUpdateStaffShift($record);

            //create accounting report
            AccountingHandler::createGLDetailsPOSEndDay($record); 
        }

    }
    
}
