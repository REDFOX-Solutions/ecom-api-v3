<?php
namespace App\Services;

use App\Http\Controllers\API\CashAccountController;
use App\Http\Controllers\API\ChartOfAccountController; 
use App\Model\PaymentMethod;
use App\Model\ShopCurrency;

class CashAccountHandler{

    
    // after created cash account to update chart of account
    public static function  create_Cash_toUpdateChartOfAcc(&$lstCashAccount){ 
    
    
        $listChartofAcc=[];
        foreach ($lstCashAccount as $key => $cashAcc) {
    
            //update chart of account field is_cash_acc = 1 if cash account choose it as parent
            if(isset($cashAcc["chart_of_acc_id"])){

                if($cashAcc["chart_of_acc_id"]){
                    $updateNewCOA = [
                        "id" => $cashAcc["chart_of_acc_id"],
                        "is_cash_acc" => 1
                    ];
                    $listChartofAcc[] = $updateNewCOA;

                }
                if(isset($cashAcc["old_chart_of_acc_id"])){
                    if( $cashAcc["chart_of_acc_id"] !=$cashAcc["old_chart_of_acc_id"]){
                        $updateOldCOA = [
                            "id" => $cashAcc["old_chart_of_acc_id"],
                            "is_cash_acc" => 0
                        ];
                        $listChartofAcc[] = $updateOldCOA;
    
                    }
                    
                }
                
            }
           
            
        }
    
        if(!empty($listChartofAcc)){
            
            $ctrChartofAccount=new ChartOfAccountController();
            $ctrChartofAccount->updateLocal($listChartofAcc); 
        }
    }
    // after update cash account to update chart of account if cash account change
    public static function updateCashAccount(&$lstCashAccount,$mapOldRecords){
        $listUpdateChartofAcc=[];

        foreach ($lstCashAccount as $key => $newcashAcc) {
    
            $cashId=$newcashAcc["id"];
            $oldCashAcc=$mapOldRecords[$cashId];

           //check if chart_of_acc_id has change

           //if change -> 
                //old chart_of_acc_id, update cash_account = 0
                //new chart_of_acc_id, update cash_account = 1
           //if not change -> nothing

           if(isset($newcashAcc["chart_of_acc_id"]) && $oldCashAcc["chart_of_acc_id"] != $newcashAcc["chart_of_acc_id"]){
            $lstCOAOldIds[] = $oldCashAcc["chart_of_acc_id"];
            $lstCOANewIds[] = $newcashAcc["chart_of_acc_id"];

           }

           foreach ($lstCOAOldIds as $index => $coaIds) {
            $updateOldCOA = [
                "id" => $coaIds,
                "is_cash_acc" => 0,
                
            ];
           }
           foreach ($lstCOANewIds as $index => $coaId) {
            $updateNewCOA = [
                "id" => $coaId,
                "is_cash_acc" => 1,
                
            ];
           }
            //update chart of account field is_cash_acc = 1 if cash account choose it as parent
            
        }
        $ctrChartofAcc=new ChartOfAccountController();

        // if(!empty($updateOldCOA)){
            
            $ctrChartofAcc->updateLocal($updateOldCOA); 
            $ctrChartofAcc->updateLocal($updateNewCOA); 

        // }
    }
    public static function updateChartofAccount($lstNewCashAccount,$mapOldRecords){
            $lstCOAOldIds=[];
            $lstCOANewIds=[];
        
            foreach ($lstNewCashAccount as $key => $newcashAcc) {
        
                $cashId=$newcashAcc["id"];
        
                $oldCashAcc=$mapOldRecords[$cashId];
        
               //check if chart_of_acc_id has change
        
               //if change -> 
                    //old chart_of_acc_id, update cash_account = 0
                    //new chart_of_acc_id, update cash_account = 1
               //if not change -> nothing
        
               if(isset($newcashAcc["chart_of_acc_id"]) && $oldCashAcc["chart_of_acc_id"] != $newcashAcc["chart_of_acc_id"]){
                $lstCOAOldIds = [
                    "id" => $oldCashAcc["chart_of_acc_id"],
                    "is_cash_acc" => 0,
                ]; 
                $lstCOANewIds = [
                    "id" => $newcashAcc["chart_of_acc_id"],
                    "is_cash_acc" => 1,
                ];
        
               }
        
                
                
            }
        
            $ctrChart=new ChartOfAccountController();
            if(!empty($lstCOAOldIds)){
                $ctrChart->updateLocal($lstCOAOldIds); 
            }
            if(!empty($lstCOANewIds)){
                $ctrChart->updateLocal($lstCOANewIds); 
            }
    }
    public static function deleteToUpdate(&$lstDeleteCashAcc){
        $deleteData=[];
        foreach($lstDeleteCashAcc as $index =>$deleteCash){
            if(isset($deleteCash["id"])){
                $delete=[
                    "id"=>$deleteCash["chart_of_acc_id"],
                    "is_cash_acc" =>0
                ];
                $deleteData []=$delete;
            }
            

        }
        $controller=new ChartOfAccountController();
            $controller->updateLocal($deleteData);
    }

    /**
     * This method to create default cash account option when user choose base currency
     * @param $companyId        String company id 
     * @return void
     * @author Sopha Pum | 17-08-2021
     */
    public static function createDefaultCAOptions($companyId){
        $mapPaymentMethod = PaymentMethod::where("is_active", 1)
                                            ->get()
                                            ->keyBy("payment_name")
                                            ->all();

        $lstShopCurrencies = ShopCurrency::where("is_base", 1)
                                        ->where("is_active", 1)
                                        ->where("company_id", $companyId)
                                        ->get()
                                        ->toArray();
        if(empty($lstShopCurrencies)){
            return;
        }

        $shopBaseCurrencyId = $lstShopCurrencies[0]["id"];

        $lstDefaultCAs = [];

        
        if(isset($mapPaymentMethod["credit card"])){
            //create ABA Pay
            $lstDefaultCAs[] = [
                "payment_method_id" => $mapPaymentMethod["credit card"]["id"], 
                "name" => "ABA Pay", 
                "currency_id" => $shopBaseCurrencyId, 
                "is_active" => 0,
                "accept_currency_ids" => $shopBaseCurrencyId,
                "company_id" => $companyId,
                "pos_usable" => 1,
                "image" => 'assets/img/banks/aba.png'
            ];

            //create AC Toanjet
            $lstDefaultCAs[] = [
                "payment_method_id" => $mapPaymentMethod["credit card"]["id"], 
                "name" => "AC ToanChet", 
                "currency_id" => $shopBaseCurrencyId, 
                "is_active" => 0,
                "accept_currency_ids" => $shopBaseCurrencyId,
                "company_id" => $companyId,
                "pos_usable" => 1,
                "image" => 'assets/img/banks/ac-logo-dark.png'
            ];
        }

        if(isset($mapPaymentMethod["cash"])){
            $lstDefaultCAs[] = [
                "payment_method_id" => $mapPaymentMethod["cash"]["id"], 
                "name" => "Cash", 
                "currency_id" => $shopBaseCurrencyId, 
                "is_active" => 0,
                "accept_currency_ids" => $shopBaseCurrencyId,
                "company_id" => $companyId,
                "pos_usable" => 1,
                "image" => 'assets/img/banks/cash.png'
            ];
        }

        if(!empty($lstDefaultCAs)){
            $caCtrler = new CashAccountController();
            $caCtrler->createLocal($lstDefaultCAs);
        }
    }
 
  
}