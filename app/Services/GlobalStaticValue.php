<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class GlobalStaticValue
{
    public static $TRANSACTION_STATUS = [
        "hold" => "hold",
        "open" => "open",
        
    ];

    public static $FORMAT_DATETIME = 'Y-m-d\TH:i:sO';
    public static $FORMAT_DATETIME_ISO = 'Y-m-d\TH:i:sO';
    public static $FORMAT_DATE = 'Y-m-d';
    public static $FORMAT_FINANCE_PERIOD = 'm-Y';

    public static $OP_START_HOUR_NAME = "operation_start_hour";
    public static $OP_END_HOUR_NAME = "operation_end_hour";
}
