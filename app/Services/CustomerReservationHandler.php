<?php


namespace App\Services;

use App\Exceptions\CustomException;
use App\Model\CustomerReservation;

class CustomerReservationHandler
{

    /**
     * Method to setup default field for customer reservation before create
     * @param $lstNewReserves       Array customer reserve table records
     * @author Sopha Pum
     */
    public static function setupDefaultFields(&$lstNewReserves){
        foreach ($lstNewReserves as $index => &$reserve) {
            $reserve['status'] = 'open';
        }
    }

    /**
     * Method to check if customer existed or not in reserve table
     * if existed, we didn't do anything
     * If not existed, we need to create new customer
     * @param lstNewReserveTables   Referrence value list rerserve table (in logic will has only 1 record)
     * @return void
     */
    public static function checkupCustomer(&$lstNewReserveTables){
        if(count($lstNewReserveTables) > 0){
            foreach ($lstNewReserveTables as $index => &$reserveTable) {
                
                //if there are customer_id field attached with reserve table, it means 
                //they have choose existed customer
                if(isset($reserveTable['customer_id'])){

                    //we need to check to make sure if the customer really existed
                }else
                //check if there are customer attached in reserve table, create customer
                if(isset($reserveTable['customer'])){
                    $customer = $reserveTable['customer'];

                    $createdCustomer = PersonAccountHandler::createPerson($customer);

                    //update field customer
                    $reserveTable["customer_id"] = $createdCustomer["id"];
                }
            }
        }
    }

    /**
     * Method to check existed active reserve table before created
     * @param $lstNewReserveTables          list reserve table
     */
    public static function checkExistedActiveReserve($lstNewReserveTables){
        $lstReserveDate = [];
        $lstReserveTime = [];

        foreach ($lstNewReserveTables as $index => $reserve) {
            $lstReserveDate[] = $reserve['reserve_date'];
            $lstReserveTime[] = $reserve['reserve_time'];
        }

        $hasReserve = false;
        if(!empty($lstReserveDate)){
            $count = CustomerReservation::whereDateIn('reserve_date', $lstReserveDate)
                                        ->whereIn('reserve_time', $lstReserveTime)
                                        ->get()
                                        ->count();
            $hasReserve = $count > 0;
        }

        if($hasReserve){
            throw new CustomException("Table already reserved!", 400);
        }
    }


}
