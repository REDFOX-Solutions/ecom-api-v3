<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Services\DataConvertionClass;

class RestResource extends Resource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $lstFiltered = array_filter(parent::toArray($request), function($var){
            return isset($var) && !empty($var);
        });

        DataConvertionClass::findLangs2ChangeIndex($lstFiltered);
        return $lstFiltered;
    }

    public function with($request){
        return [
            'version' => '1.0.0',
            'author' => url('https://www.redfox-ws.com')
        ];
    }
}
