<?php

namespace App\Services;

use App\Model\ReservedRoom;
use Illuminate\Database\Eloquent\Model;

class ReservedRoomPriceHandler extends Model
{

    /**
     * Method get reserved room to recalculate reservation price
     * @param $lstReservedRoomPrices    array reserved room price just created/updated
     */
    public static function recalcReservationPrice($lstReservedRoomPrices){

        //get unit reserved room id
        $reservedRoomIds = [];
        foreach ($lstReservedRoomPrices as $key => $reservedRoomPrice) {
            $reservedRoomIds[] = $reservedRoomPrice["reserved_room_id"];
        }
        $setResvRoomIds = array_unique($reservedRoomIds);

        $lstReservRooms = ReservedRoom::whereIn("id", $setResvRoomIds)
                                        ->get()
                                        ->toArray();

        $reservationIds = [];
        foreach ($lstReservRooms as $key => $reservedRoom) {
            $reservationIds[] = $reservedRoom["reservation_id"];
        }

        if(isset($reservationIds) && !empty($reservationIds)){
            $setResvIds = array_unique($reservationIds);

            foreach ($setResvIds as $key => $resvId) {
                ReservationHandler::reCalcPrice($resvId);
            }
        }
    }
}
