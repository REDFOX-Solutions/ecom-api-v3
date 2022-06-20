<?php

namespace App\Services;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\API\ReceivableAccountController;

use App\Http\Controllers\API\ReceivableAccountDetailController;
use Carbon\Carbon;


class ReceivableAccountHandler{
    public static function setDefault(&$lstReceivable){
        // foreach ($lstReceivable as $key => &$value) {
        //     if(empty($value["ref_number"]) || $value["ref_number"]==null){
        //         $str="rca";
        //         $ramdom=$str.mt_rand(100000,999999);

        //         $value["ref_number"]=$ramdom;
        //     }
        //     if(empty($value["status"])){
        //         $value["status"]=1;
        //     }
        // }
    }
    public static function createReceivableDetail($lstReceivable){
        // foreach ($lstReceivable as $key => $receivable) {
        //     $lstReceivables=[];

        //     if(isset($receivable["receivable_detail"])){
        //         $receivableDetail=$receivable["receivable_detail"];
        //         foreach ($receivableDetail as $index => &$value) {
        //             $value["receivable_account_id"]=$receivable["id"];
        //             $lstReceivables[]=$value;

        //             # code...
        //         }
        //     }
        //     $receivableCtr=new ReceivableAccountDetailController();
        //     $receivableCtr->createLocal($lstReceivables);
        //     # code...
        // }

    }
  
}

?>