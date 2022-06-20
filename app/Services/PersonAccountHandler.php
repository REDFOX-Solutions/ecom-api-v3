<?php

namespace App\Services;

use App\Http\Controllers\API\PersonAccountController;
use App\Model\PersonAccount;
use Illuminate\Database\Eloquent\Model;

class PersonAccountHandler
{

    public static $PERSON_CUSTOMER_TYP = 'customer';

    public static function setupDefaultFieldOnCreate(&$lstNewPeople){

    }
    /**
     * Method to get default customer
     * This method will create a new default customer if it did't have in DB
     */
    public static function getDefaultCustomer(){
        //get default customer to update sales order customer if it didn't specific
        $lstDefaultCustomers = PersonAccount::where("person_type", self::$PERSON_CUSTOMER_TYP)
                                            ->where("is_default", 1)
                                            ->get()
                                            ->toArray();

        //if there are no a default customer, we will create a new one
        if(empty($lstDefaultCustomers)){
            $newCustomer = [
                "phone" => "N/A", 
                "email" => "N/A", 
                "person_type" => self::$PERSON_CUSTOMER_TYP, 
                "person_code" => "cusDefault", 
                "is_active" => 1, 
                "status" => "active", 
                "is_default" => 1
            ];
            $controller = new PersonAccountController();

            $lstDefaultCustomers = $controller->createLocal([$newCustomer]);
        }

        return $lstDefaultCustomers[0];
    }

    /**
     * Method create Person Account by checking phone number first
     */
    public static function createPerson($person){

        //if there are no phone, we don't do anything because person required phone number
        if(isset($person["phone"])){
            $lstExistedPerson = PersonAccount::where('phone', $person["phone"])
                                            ->get()
                                            ->toArray();
            if(empty($lstExistedPerson)){
                $personCtler = new PersonAccountController();
                $lstExistedPerson = $personCtler->createLocal([$person]);
            }

            return $lstExistedPerson[0];
        }

        return null;
    }
}
