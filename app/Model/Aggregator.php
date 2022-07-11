<?php

namespace App\Model;

class Aggregator extends MainModel
{
    protected $table = "aggregators";

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'id',
        'txn_date',
        'file_name',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'id' => 'string',
        'file_size' => 'integer'
    ];
}
