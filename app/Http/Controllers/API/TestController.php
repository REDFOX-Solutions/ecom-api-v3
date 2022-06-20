<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\Company;
use App\Model\User;
use App\Services\GlobalStaticValue;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function testDate(){
        
        $lstUsers = User::get()->toArray();

        $lstCompanies = Company::where("id", $lstUsers[0]["company_id"])
                                ->get()
                                ->toArray();
        $companyTimeZone = isset($lstCompanies[0]["default_timezone"]) ? $lstCompanies[0]["default_timezone"] : "UTC";

        $timezones = DateTimeZone::listIdentifiers();   
        $mapGMTTimezone = [];
        $lstGMTs = [];
        foreach ($timezones as $key => $zone) {
            $gmt = Carbon::now($zone)->format("\G\M\TP");
            $lstGMTs[] = $gmt;
            $mapGMTTimezone[$gmt][] = $zone;
        }

        ksort($mapGMTTimezone);

        $lstTimezones = [];
        foreach ($mapGMTTimezone as $gmt => $zones) {
            foreach ($zones as $key => $zone) {
                $lstTimezones[] = ["label" => "($gmt) $zone", "value" => $zone];
            }
        }

        return $lstTimezones;

        return DateTimeZone::listIdentifiers();  
        return Carbon::createFromFormat(GlobalStaticValue::$FORMAT_DATETIME, "2021-07-23T09:59:49+0000")->setTimezone($companyTimeZone)->format("H:00");
        return Carbon::now($companyTimeZone)->format(GlobalStaticValue::$FORMAT_DATETIME);
    }
}
