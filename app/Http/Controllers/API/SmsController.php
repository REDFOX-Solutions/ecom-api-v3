<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Nexmo\Laravel\Facade\Nexmo;
class SmsController extends Controller
{
    public function index(Request $request){
                  
            $message =   Nexmo::message()->send([
            'to' => '855' . $request->mobile,
            'from' => '855' . $request->from,
            'text' => $request->text,
            
                ]);
            if( $message == true){
            return 'successfully';
            }else{
            return 'failed';
            }
            }
        
}
