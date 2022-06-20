<?php

namespace App\Model;

class ReservedRoom extends MainModel
{
    protected $table = 'reserved_room';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date'; 

    protected $fillable = [
        "id", 
        "created_date", 
        "created_by_id", 
        "updated_date", 
        "updated_by_id",
        "is_backup",
        "reservation_id", 
        "room_id", 
        "price",
        "pax"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "price" => "double",
        "pax" => "integer",
        "total_price_f" => "double"
    ];

    protected $appends = ["total_price_f"];

    // To auto create children records
    public static $relationship = [
        "children" => [ 
            ["name" => "reserved_room_price_r", "parent_field" => "reserved_room_id", "controller" => "App\Http\Controllers\API\ReservedRoomPriceController"]
        ]
    ];
    

    public function getTotalPriceFAttribute(){
        $lstReservedRoomPrices = ReservedRoomPrice::where("reserved_room_id", $this->id)
                                                    ->get()
                                                    ->toArray();
        $totalPrice = 0;

        if(isset($lstReservedRoomPrices) && !empty($lstReservedRoomPrices)){
            foreach ($lstReservedRoomPrices as $key => $reservedPrice) {
                $totalPrice += isset($reservedPrice["price"]) ? : 0;
            }
        }

        return $totalPrice;

    }

    public function reservationR(){
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function roomR(){
        return $this->belongsTo(Products::class, 'room_id')->with("properties");
    }

    public function reservedRoomPriceR(){
        return $this->hasMany(ReservedRoomPrice::class, 'reserved_room_id', 'id');
    }

    
}
