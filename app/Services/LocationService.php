<?php

namespace App\Services;

use Illuminate\Support\Fluent;
use Stevebauman\Location\Position;
use Stevebauman\Location\Drivers\Driver;

class LocationService extends Driver
{
    public function url()
    {
        return '';
    }

    protected function hydrate(Position $position, Fluent $location)
    {
        $position->countryCode = $location->country_code;

        return $position;
    }

    protected function process($ip)
    {
        try {
            $response = json_decode(file_get_contents($this->url().$ip), true);

            return new Fluent($response);
        } catch (\Exception $e) {
            return false;
        }
    }
}
