<?php

namespace App\Services;

use App\Http\Controllers\API\ShopCurrencyController;
use App\Model\Company;
use App\Model\ShopCurrency;
use Illuminate\Support\Facades\Auth;

class CompanyHandler
{
    /**
     * Method to setup field before create company
     * @param $lstNewCompanies      Ref list company records
     * @created Sopha Pum | 02-05-2021
     */
    public static function setupFieldBeforeCreate(&$lstNewCompanies){
        foreach ($lstNewCompanies as $key => &$newCompany) {
            
            //setup domain name. If domain empty, we will get name as domain
            if(!isset($newCompany["domain"])){
                $name = $newCompany["langs"]["en"]["name"];
                $newCompany["domain"] = Helper::generateDomain($name);
            }
        }
    }

    /**
     * Method to auto create shop base currency when we setup base currency for company
     * @param $lstNewCompanies      Ref list company records
     * @created Sopha Pum | 02-05-2021
     */
    public static function createShopBaseCurrency($lstNewCompanies){

        $lstNewShopCurrencies = [];
        foreach ($lstNewCompanies as $key => $company) { 

            if(!isset($company["base_currency_id"])) continue; 

            //check if shop has currency already
            $lstShopCurrency = ShopCurrency::where("company_id", $company["id"])
                                            ->where("is_base", 1)
                                            ->get()
                                            ->toArray();
            
            //if Company has base currency already, we dont need to create shop currency
            if(empty($lstShopCurrency)){
                $lstNewShopCurrencies[] = [
                    "is_base" => 1, 
                    "developer_name", 
                    "rate_to_base" => 1, 
                    "ordering" => 1, 
                    "company_id" => $company["id"], 
                    "rate_method" => "mul", 
                    "is_active" => 1, 
                    "pos_usable" => 1, 
                    "currency_picklist_id" => $company["base_currency_id"]
                ];

                

            }
        }

        if(!empty($lstNewShopCurrencies)){
            $shopCurrencyController = new ShopCurrencyController();
            $shopCurrencyController->createLocal($lstNewShopCurrencies);

            //create default cash account option when user choose base currency but not active it
            //*** it need to wait after we create shop currency
            foreach ($lstNewShopCurrencies as $key => $shopCurrency) { 
                CashAccountHandler::createDefaultCAOptions($shopCurrency["company_id"]);
            }
            
        }
    }
    public static function createShopBaseCurrencyAfterUpdate($lstNewCompanies, $mapOldCompanies){
        $lstCreateShopCurrencies = [];
        foreach ($lstNewCompanies as $key => $newCompany) {
            $oldCompany = $mapOldCompanies[$newCompany["id"]];
            
            //if there are no base currency and then we setup base currency, we will create shop currency
            if(isset($newCompany["base_currency_id"]) && $oldCompany["base_currency_id"] != $newCompany["base_currency_id"])
            {
                $lstCreateShopCurrencies[] = $newCompany;
            }
        }
        if(!empty($lstCreateShopCurrencies)){
            self::createShopBaseCurrency($lstCreateShopCurrencies);
        }
    }


    /**
     * Get company ledger id
     */
    public static function getCompanyLedgerId(){
        //get ledger id from company to populate in JE
        $ledger_id = null; 
        $company_id = Auth::User()->company_id;
        $lstCompany = Company::where("id", $company_id)
                                ->get()
                                ->toArray();

        if(!empty($lstCompany)){
            $ledger_id = isset($lstCompany[0]["ledger_id"]) ? $lstCompany[0]["ledger_id"] : null;
        }

        return $ledger_id;
    }

}
