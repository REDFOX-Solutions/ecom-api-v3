<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CashAccounts;
use App\Http\Resources\RestResource;
use App\Services\DatabaseGW;

class CashAccountController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'cash_accounts',
            'model' => 'App\Model\CashAccounts',
            'prefixId' => 'CA'
        ];
    }

    public function getQuery(){
        return CashAccounts::query();
    }

    public function getModel(){
        return 'App\Model\CashAccounts';
    }

    public function beforeCreate(&$lstNewRecords)
    {
        foreach ($lstNewRecords as $key => &$newCashAcc) {
            //check to auto put chart of account
            if(!isset($newCashAcc["chart_of_acc_id"])){
                $newCashAcc["chart_of_acc_id"] = ChartOfAccHandler::createCOAForCashAccount($newCashAcc["name"])["id"];
            }
        }
    }
    public function publicPaymentMethod(Request $req){ 
        try{ 
            $lstFilter = $req->all(); 
            $lstFilter['is_active'] = 1;
            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $lstFilter));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        } 
    }
}
