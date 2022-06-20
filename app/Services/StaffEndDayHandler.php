<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\StaffShiftController;
use App\Model\StaffShifts;

class StaffEndDayHandler extends Model
{
    /**
    * to do logic related with StaffEndDay
    * @createdDate: 06-22-2020
    * @author: phanith
    * @company: redfox web solutions (www.redfox-ws.com) * @changelog:
    */
     
    public static function setDefaultFieldsValue(&$lstNewRecords){

        foreach ($lstNewRecords as $key => &$staffEndDay) {
            $staffEndDay["end_by_id"] = Auth::user()->id;
        }
    } 
    /**
    * purpose of this method update staff_end_day_id after create staff-end-days success
    * @param PARAM_TYPE new list of $lstStaffEndDay * @param $$lstStaffEndDay
    * @createdDate: 06-22-2020
    * @author: phanith
    */
    public static function updateStaffShift($lstStaffEndDay){
        $lstNewStaffShifts = array(); // create new lst for store value if end_date equal 2 transaction_date & staff_end_day_id = null
        $collection = collect($lstStaffEndDay);//make $lstStaffEndDay from lst 2 collection
        $keyed = $collection->keyBy('end_date')->keys()->all();// get all keys from collection
    
        $oldLstStaffShifts =  StaffShifts::whereIn("transaction_date", $keyed)->where('staff_end_day_id',null)->orderBy('transaction_date', 'asc')->get()->toArray(); //get StaffShifts with condition
       
      
       foreach ($lstStaffEndDay as $key => $staffEndDay){

            foreach($oldLstStaffShifts as $index => $oldStaffShift){

                if($staffEndDay['end_date'] == $oldStaffShift['transaction_date']){
            
                    $lstNewStaffShifts[]=['staff_end_day_id'=>$staffEndDay['id'],'id'=>$oldStaffShift['id']];// push id of staffEndDay & id of staffShip to new list 
                   
                // array_push($lstNewStaffShifts,['staff_end_day_id'=>$staffEndDay['id'],'id'=>$oldStaffShift['id']]); 
                    
                }
        }
           
       } 
    //    dd($lstNewStaffShifts);
       if(isset($lstNewStaffShifts)){ //if lstStaffShift has data
            $staffShiftController = new StaffShiftController(); 
            $staffShiftController->updateLocal($lstNewStaffShifts); // update staff_end_day_id in StaffShift
       }
  
    }
}
