<?php

namespace App\Services;

use App\Http\Controllers\API\ChartOfAccountController;
use App\Model\AccountingClass;
use App\Model\Categories;
use App\Model\ChartOfAccount;
use App\Model\MetaDataConfig;
use Illuminate\Support\Facades\Auth;

class ChartOfAccHandler 
{

    /**
     * Method to create auto chart of account for cash account
     * @param $cashName     String Cash Account Name
     * @return $new chart of account record
     */
    public static function createCOAForCashAccount($cashName){
        $lstAccCls = AccountingClass::where("class_type", "asset")
                                    ->where("is_active", 1)
                                    ->get()
                                    ->toArray();

        if(!empty($lstAccCls)){
            $newCOA = [
                "accounting_class_id" => $lstAccCls[0]["id"],
                "is_active" => 1,
                "is_cash_acc" => 1,
                "name" => $cashName ,
                "system_default" => 0,
                "code" => DatabaseGW::generateAutoCode("cashAccountAsset", $lstAccCls[0]["code"])
            ];
            $coaCtrl = new ChartOfAccountController();
            $lstNewCOAs = $coaCtrl->createLocal([$newCOA]);
            return $lstNewCOAs[0];
        }

        return null;
        
    }

    /**
     * Method to log Chart of account transaction from released GL (Jurnal Entry)
     * - Log COA Transaction Summary
     * - Log COA Transaction Detail
     * @param $lstNewGLs        list new GL records (updated record)
     * @param $mapOldGLs        mapping old GL records
     * @return void
     * @author Sopha Pum
     */
    public static function logCOAActionFromGL($lstNewGLs, $mapOldGLs){
        $lstReleasedGLs = [];
        $lstReleasedAccClsIds = [];

        foreach ($lstNewGLs as $index => $newGL) {
            $oldGL = null;
            if(!is_null($mapOldGLs) && isset($mapOldGLs[$newGL["id"]])) $oldGL = $mapOldGLs[$newGL["id"]];

            //if GL changed status to release, we will log COA transaction
            if(isset($newGL["status"]) && $newGL["status"] == 'released' && (is_null($oldGL) || $oldGL["status"] != $newGL["status"])){
                $lstReleasedGLs[] = $newGL;
                $lstReleasedAccClsIds[] = $newGL["accounting_class_id"];
            }
        }

        
    }

    /**
     * Method to setup default chart of account for person account fields
     * @param $newPerson           ref new person record
     * @return void
     * @author Sopha Pum | 15-07-2021
     */
    public static function setupDefaultCOAforPerson(&$newPerson){ 
        
        if(!isset($newPerson["personal_coa_id"]) && isset($newPerson["record_type"])){

            $defaultCOA = ChartOfAccount::where("system_default", 1)
                                        ->get()
                                        ->keyBy("code")
                                        ->all();

            $recordType = $newPerson["record_type"];

            if(strtolower($recordType) == 'customer'){
                $newPerson["personal_coa_id"] = $defaultCOA["11000"]["id"];
            }
            if(strtolower($recordType) == 'vendor'){
                $newPerson["personal_coa_id"] = $defaultCOA["20000"]["id"];
            }
        }
    }

    /**
     * Method to setup default chart of account for product fields
     * @param $newProduct           ref new product record
     * @return void
     * @author Sopha Pum | 15-07-2021
     */
    public static function setupDefaultCOAforProduct(&$newProduct){

        $userInfo = Auth::user(); 
        $lstMetaConfigs = MetaDataConfig::where("name", "enable_accounting")
                                        ->where("company_id", $userInfo["company_id"])
                                        ->get()
                                        ->toArray();

        //if there are enable accounting, we dont need to auto update chart of account
        if(count($lstMetaConfigs) > 0 && $lstMetaConfigs[0]["value"] == "1") return;

        $defaultCate = [];
        //get record from product category 
        if(isset($newProduct["default_category_id"])){
            $lstCategories = Categories::where("id", $newProduct["default_category_id"])
                                        ->get()
                                        ->toArray();

            $defaultCate = $lstCategories[0];
        }

        //get all default chart of account
        $defaultCOA = ChartOfAccount::where("system_default", 1)
                                        ->get()
                                        ->keyBy("code")
                                        ->all();

        //setup default COA for product by get it from Category or default COA
        if(!isset($newProduct["inventory_coa_id"])){
            $newProduct["inventory_coa_id"] = isset($defaultCate["inventory_coa_id"]) ? $defaultCate["inventory_coa_id"] : $defaultCOA["12000"]["id"];
        }
        if(!isset($newProduct["sale_coa_id"])){
            $newProduct["sale_coa_id"] = isset($defaultCate["sale_coa_id"]) ? $defaultCate["sale_coa_id"] : $defaultCOA["40000"]["id"];
        }
        if(!isset($newProduct["cogs_coa_id"])){
            $newProduct["cogs_coa_id"] = isset($defaultCate["cogs_coa_id"]) ? $defaultCate["cogs_coa_id"] : $defaultCOA["50000"]["id"];
        }
        if(!isset($newProduct["std_cost_var_coa_id"])){
            $newProduct["std_cost_var_coa_id"] = isset($defaultCate["std_cost_var_coa_id"]) ? $defaultCate["std_cost_var_coa_id"] : $defaultCOA["50000"]["id"];
        }
        if(!isset($newProduct["std_cost_rev_coa_id"])){
            $newProduct["std_cost_rev_coa_id"] = isset($defaultCate["std_cost_rev_coa_id"]) ? $defaultCate["std_cost_rev_coa_id"] : $defaultCOA["50000"]["id"];
        }
        if(!isset($newProduct["po_accrual_coa_id"])){
            $newProduct["po_accrual_coa_id"] = isset($defaultCate["po_accrual_coa_id"]) ? $defaultCate["po_accrual_coa_id"] : $defaultCOA["12000"]["id"];
        }
        if(!isset($newProduct["deferral_coa_id"])){
            $newProduct["deferral_coa_id"] = isset($defaultCate["deferral_coa_id"]) ? $defaultCate["deferral_coa_id"] : $defaultCOA["50000"]["id"];
        }
    }
}
