<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestResource;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Http\Request;

class PicklistController extends Controller
{
    public function getTimezone(){
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
        return new RestResource(collect($lstTimezones));
    }

    public function getIndustry(){
        $lstIndustry = [
            ["label" => "Food, Beverage & Restaurant", "value" => "restaurant"],
            ["label" => "Shop, Retail & Wholesale", "value" => "shop"]
        ];

        return new RestResource(collect($lstIndustry));
    }
}
