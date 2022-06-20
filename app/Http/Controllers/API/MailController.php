<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;
class MailController extends Controller
{

  /**  Open your .env file which is located in your root directory and you will find below code related to email settings.

* MAIL_DRIVER=smtp
* MAIL_HOST=smtp.googlemail.com
* MAIL_PORT=465
* MAIL_USERNAME=ENTER_YOUR_GMAIL_USERNAME
* MAIL_PASSWORD=ENTER_YOUR_GMAIL_PASSWORD
* MAIL_ENCRYPTION=ssl
*/
    public function index(Request $req){
    
        $to_name = 'company_name';
        // $to_email = '';
        
        Mail::send([], [], function($message) use ($to_name, $req) {
            $message->to($req['to_email'], $to_name)
                    ->subject($req['subject'])->setBody($req['body']); ;
                    
            $message->from($req['from_email_address'],$to_name);
        });  
       
     return 'message sent to :'.$req['to_email'];
    }

}
