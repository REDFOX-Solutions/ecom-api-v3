<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ErrorController 
{
    
    public function _404(){
        return response()->json("You have request incorrect route!", 404);
    }
}
