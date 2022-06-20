<?php

namespace App\Model;



class CustomerReservation extends MainModel
{
    protected $table = 'customer_reservation';
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
        "table_id",
        "customer_id",
        "reserve_date",
        "reserve_time",
        "status"
    ];

    protected $casts = [
        "id" => "string",
        "is_backup" => "integer"
    ];

    protected $appends = [];

    /**
     * Get the customer that owns the CustomerReservation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(PersonAccount::class, 'customer_id');
    }

    /**
     * Get the FloorTable that owns the CustomerReservation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function table()
    {
        return $this->belongsTo(FloorTable::class, 'table_id');
    }
}
