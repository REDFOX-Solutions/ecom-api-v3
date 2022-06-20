<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReservedRoomPrice extends MainModel
{
    protected $table = 'reserved_room_price';
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
        "stay_date", 
        "price", 
        "reserved_room_id",
        "discount"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "price" => "double",
        "discount" => "double"
    ];

    protected $appends = [];

    public function reservedRoomR(){
        return $this->belongsTo(ReservedRoom::class, 'reserved_room_id');
    }
}
