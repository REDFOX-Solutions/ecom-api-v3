<?php

namespace App\Http\Controllers\API;

use App\Exceptions\CustomException;
use App\Http\Resources\RestResource;
use App\Model\PersonAccount;
use Illuminate\Http\Request;
use App\Model\Reservation;
use App\Services\ReservationHandler;

class ReservationController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "reservation",
            "model" => "App\Model\Reservation", 
            "prefixId" => "051"
        ];
    }

    public function getQuery(){
        return Reservation::query();
    }

    public function getModel(){
        return "App\Model\Reservation";
    }
    
    public function getCreateRules(){
        return [];
    }

    public function getUpdateRules(){
        return [
            "id" => "required"
        ];
    }

    public function beforeCreate(&$lstNewRecords){
        ReservationHandler::createDefaultFields($lstNewRecords);
    }
    
    public function publicStore(Request $req){ 
        $this->noAuth = true;
        return $this->store($req);
    }

    public function guestRequestReservation(Request $req){
        $lstReservations = $req->all();

        $lstReservationCreated = [];
 
        if(isset($lstReservations) && !empty($lstReservations)){
            foreach ($lstReservations as $ind => &$reservation) {
                //create customer record
                if(isset($reservation["guest_r"])){
                    $newGuest = $reservation["guest_r"];

                    if(!isset($newGuest["phone"])){
                        throw new CustomException("Phone is required!", 404);
                    }

                    //get existed guest by phone number
                    $lstCreatedGuests = PersonAccount::where("phone", $newGuest["phone"])->get()->toArray();

                    //if there are no existed guest, create a new one
                    if(!isset($lstCreatedGuests) || empty($lstCreatedGuests)){
                        $personCtrler = new PersonAccountController();
                        $lstCreatedGuests = $personCtrler->createLocal([$newGuest]);
                    }
                    $reservation["guest_id"] = $lstCreatedGuests[0]["id"];
                }
            }
 
            //create Reservation and it child will auto create 
            $lstReservationCreated = $this->createLocal($lstReservations);
            $lstIds = [];
            foreach ($lstReservationCreated as $key => $value) {
                $lstIds[] = $value["id"];
            } 
            return RestResource::collection(Reservation::whereIn("id", $lstIds)->get()); 
        }
 
        return $this->respondError("Invaild record.");
        
        
    }
}
