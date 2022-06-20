<?php
namespace App\Services;
use Mail;
class MailHandler{

   

    /**
    * work with send email 
    * @param PARAM_TYPE config an object for send one email to another email
    * @param  "to_email":"nochphanith@gmail.com",
    * @param    "from_email_address":"bongdjnith@gamil.com",
    * @param   "subject":"chatbot",
    * @param   "body":"Hello i am a robot i have no life "
    * @createdDate: 05-05-2020
    * @author: phanith
    */
    public static function sendEmail($config){
        $to_name = 'company_name'; // here we can specific compony name 
        Mail::send([], [], function($message) use ($to_name, $config) {//get request by params 
        $message->to($config['to_email'], $to_name) // user can send to the email that there wish 
        
        ->subject($config['subject'])->setBody($config['body']);// get subject and body

        $message->from($config['from_email_address'],$to_name);//send from our email 
        });  

        return 'message sent to :'.$config['to_email'];  // response to the user can see message send success or not

    }
    
}
