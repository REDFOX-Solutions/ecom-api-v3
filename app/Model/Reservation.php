<?php

namespace App\Model;

use Carbon\Carbon;
 
class Reservation extends MainModel
{
    protected $table = 'reservation';
    protected $keyType = 'string';
    // protected $dateFormat = 'Y-m-d\TH:i:s.uP';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id", 
        "created_by_id",
		"updated_by_id",
		"created_date",
		"updated_date",
		"is_backup",
		"guest_id",
		"checkin_date",
		"checkout_date",
		"pax",
		"channel_id", 
		"channel_code",
        "total_price",
        "total_discount",
        "total_amount",
        "status",//waiting hotel confirmation, canceled, confirmed
        "message_sent",
        "message_received",
        "name",//auto generate 
		"num_of_room",
        "cancel_reason"
    ];

    protected $casts = [
        "is_backup" => "integer", 
        "pax" => "integer",
        "total_price" => "double",
        "total_discount" => "double",
        "total_amount" => "double",
        "message_sent" => "integer",
        "message_received" => "integer",
        "num_of_night_c" => "integer",
        "num_of_room" => "integer"
    ];

    protected $appends = ["num_of_night_c"]; 

    // To auto create children records
    public static $relationship = [
        "children" => [ 
            ["name" => "reserve_details_r", "parent_field" => "reservation_id", "controller" => "App\Http\Controllers\API\ReservedRoomController"]
        ]
    ];
 
    //Formula fields
    public function getNumOfNightCAttribute(){

        if(!isset($this->checkin_date) || !isset($this->checkout_date)){
            return 0;
        }

        $checkIn = Carbon::parse($this->checkin_date);
        $checkOut = Carbon::parse($this->checkout_date);
        $nightsDays = $checkIn->diffInDays($checkOut);

        return $nightsDays;
    }

    // relationships
    public function guestR(){
        return $this->belongsTo(PersonAccount::class, 'guest_id');
    }

    public function channelR(){
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function reserveDetailsR(){
        return $this->hasMany('App\Model\ReservedRoom', 'reservation_id', 'id')
                    ->with("roomR");
    }

}
