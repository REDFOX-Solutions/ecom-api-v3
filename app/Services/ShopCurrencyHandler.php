<?php

namespace App\Services;

use App\Http\Controllers\API\CurrencyExchangeRateController;
use App\Http\Controllers\API\ShopCurrencyController;
use App\Model\CashAccounts;
use App\Model\CurrencyExchangeRate;
use App\Model\ShopCurrency;
use Illuminate\Database\Eloquent\Model;

class ShopCurrencyHandler
{
    /**
     * Method to get Company Base Currency 
     * If there are no Base Currency found, we will create USD as base currency
     */
    public static function getBaseCurrency(){
        $lstCurrencies = ShopCurrency::where("is_active", 1)
                                        ->where("is_base", 1)
                                        ->get()
                                        ->toArray();

        //if there are no base currency, we will create 1 base currency USD
        if(!isset($lstCurrencies) || count($lstCurrencies) <= 0){
            $newBaseCurrency = [
                "is_base" => 1,
                "is_active" => 1,
                "label" => "USD",
                "developer_name" => "usd",
                "ordering" => 1,
                "company_id" => Auth::user()->company_id,
                "symbo" => "USD"
            ];
            $shopCur = new ShopCurrencyController();
            $lstNewBaseCurs = $shopCur->createLocal([$newBaseCurrency]);

            $baseCurrency = $lstNewBaseCurs[0];

            //change currency exchange
            $newExchangeRate = [
                "from_currency_id" => $baseCurrency["id"], 
                "to_currency_id" => $baseCurrency["id"], 
                "method" => "mul", 
                "rate" => 1
            ];
            $exchangeController = new CurrencyExchangeRateController();
            $exchangeController->createLocal([$newExchangeRate]);
            
            return $baseCurrency;
        }

        return $lstCurrencies[0];
    }


    /**
     * Method to calculate base amount for receipt
     * @param $lstReceipts      Ref List receipts
     * @return void
     * @created 20-02-2021
     * @author Spoha Pum
     */
    public static function calcReceiptBaseAmount(&$lstReceipts){

        //get company base currency to calc 
        $baseCurrency = self::getBaseCurrency(); 

        //get company exchange rate by mapping with "from id"
        $mapExchangeRates = CurrencyExchangeRate::where("to_currency_id", $baseCurrency["id"]) 
                                                ->get()
                                                ->mapWithKeys(function ($item){
                                                    return [$item["from_currency_id"] => $item];
                                                })
                                                ->all(); 

        $mapCashAccs = CashAccounts::where("pos_usable", 1)
                                    ->where("is_active", 1)
                                    ->get()
                                    ->keyBy(function($item){
                                        return $item["id"];
                                    })
                                    ->all();

        //check list receipts with currency exchange first, if there are no exchange, we will return error
        // foreach ($mapCashAccs as $cashAccId => $cashAcc) {
        //     $currencyId = $cashAcc["currency_id"];
        //     if(!isset($mapExchangeRates[$currencyId])){
        //         throw new CustomException("Invalid Currency!", 404);
        //     }
        // } 

        //calculate amount to base currency 
        foreach($lstReceipts as $index => &$receipt){ 

            if(!isset($receipt["cash_account_id"])){
                throw new CustomException("Payment Type is required!", 404);
            }
            $cashAccId = $receipt["cash_account_id"];
            $cashAcc = isset($mapCashAccs[$cashAccId]) ? $mapCashAccs[$cashAccId] : [];

            //get cash account currency to get exchange rate, 
            //if there are no match cash acc, we will auto use base currency
            $currencyId = isset($cashAcc["currency_id"]) ? $cashAcc["currency_id"] : $baseCurrency["id"];

            //get exchange rate from currency.
            $currencyExchange = isset($mapExchangeRates[$currencyId]) ? $mapExchangeRates[$currencyId] : [];

            $exchangeRate = isset($currencyExchange["rate"]) ? $currencyExchange["rate"] : 1;
            $exchangeMethod = isset($currencyExchange["method"]) ? $currencyExchange["method"] : 'mul';
            $receiptAmount = $receipt["amount"];
            
            $receipt["amount_base_currency"] = ($exchangeMethod == 'div' ? ($receiptAmount / $exchangeRate) : ($receiptAmount * $exchangeRate));
        }
    }
}
