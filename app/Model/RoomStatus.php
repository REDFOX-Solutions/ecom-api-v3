<?php

namespace App\Model;

class RoomStatus extends MainModel
{
    protected $table = 'room_status';
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
        "room_id", 
        "status", 
        "checkin_date", 
        "checkout_date", 
        "cancel_reason"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer"
    ];

    protected $appends = [];

    public function roomR(){
        return $this->belongsTo(Products::class, 'room_id');
    }
}
