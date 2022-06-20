<?php

namespace App\Services;

use App\Http\Controllers\API\ReservationController;
use App\Model\ReservedRoom;

class ReservationHandler
{
    /**
     * Method to create default field when reservation is creating
     */
    public static function createDefaultFields(&$lstNewReservation){

        foreach ($lstNewReservation as $key => &$reservation) {
            $reservation["name"] = DatabaseGW::generateReferenceCode('reservation');
        }
    }

    /**
     * Method to re-calculation price when reserved room price is created/updated
     * @param $recordId     Reservation record id
     */
    public static function reCalcPrice($recordId){

        $lstReservedRoom = ReservedRoom::where("reservation_id", $recordId)
                                        ->with("reservedRoomPriceR")
                                        ->get()
                                        ->toArray();

        $totalAmount = 0;
        $totalDiscount = 0;
        if(isset($lstReservedRoom) && !empty($lstReservedRoom)){
            foreach ($lstReservedRoom as $key => &$reservedRoom) {

                //get each price to calculate
                if(isset($reservedRoom["reserved_room_price_r"])){
                    $lstRoomPrices = $reservedRoom["reserved_room_price_r"];

                    //sum all prices
                    if(!empty($lstRoomPrices)){
                        foreach ($lstRoomPrices as $key => $roomPrice) {
                            $totalAmount += isset($roomPrice["price"]) ? $roomPrice["price"] : 0;
                            $totalDiscount += isset($roomPrice["discount"]) ? $roomPrice["discount"] : 0;
                        }
                    }
                }
            }

            $reservation = [
                "id" => $recordId,
                "total_price" => ($totalAmount - $totalDiscount),
                "total_discount" => $totalDiscount,
                "total_amount" => $totalAmount,
            ];
            $reservationController = new ReservationController();
            $reservationController->updateLocal([$reservation]);

        }


        
    }
}
