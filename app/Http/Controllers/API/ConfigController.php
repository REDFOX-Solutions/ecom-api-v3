<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHandler;
use App\Model\AccountingBook;
use App\Model\AccountingClass;
use App\Model\ChartOfAccount;
use App\Model\GLAccMapping;
use App\Model\Products;
use App\Services\AccountingHandler;

class ConfigController extends Controller
{
    public function setupDefaultAccounting(Request $request){

        AccountingHandler::createDefaultAccounting();
        return ResponseHandler::showSuccess(["is_success"=> 1]);
 
    }
}
