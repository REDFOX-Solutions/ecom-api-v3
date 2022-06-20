<?php

namespace App\Services;

use App\Http\Controllers\API\CurrencyExchangeRateHisController;
use Carbon\Carbon;

class CurrencyExchangeRateHandler 
{

    /**
     * Method to create Currency Exchange History
     * @param $mapOldExchangeRate
     * @param $lstNewExchangeRates
     * @return void
     * @created 22-02-2021
     * @author Sopha Pum
     */
    public static function createHistory($mapOldExchangeRate, $lstNewExchangeRates){

        $lst2UpdateHistories = [];

        foreach ($lstNewExchangeRates as $inx => $newRate) {
            $oldRate = $mapOldExchangeRate[$newRate["id"]];

            if(isset($newRate["rate"]) && $oldRate["rate"] != $newRate["rate"]){
                $history = [
                    "exchange_date" => Carbon::now()->format(GlobalStaticValue::$FORMAT_DATETIME), 
                    "from_currency_id" => $oldRate["from_currency_id"], 
                    "to_currency_id" => $oldRate["to_currency_id"], 
                    "method" => $oldRate["method"], 
                    "rate" => $oldRate["rate"]
                ];

                $lst2UpdateHistories[] = $history;
            }
        }

        if(count($lst2UpdateHistories) > 0){
            $historyController = new CurrencyExchangeRateHisController();
            $historyController->createLocal($lst2UpdateHistories);
        }
    }
}
