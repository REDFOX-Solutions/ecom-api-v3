<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Http\Controllers\API\GLAccMappingController;
use App\Http\Controllers\API\PricebookController;
use App\Http\Controllers\API\PricebookEntryController;
use App\Http\Controllers\API\ProductPropertyController;
use App\Http\Controllers\API\ProductsController;
use App\Http\Controllers\API\ProductStandardCostController;
use App\Model\GLAccMapping;
use App\Model\Pricebook;
use App\Model\PricebookEntry;
use App\Model\Products;
use App\Model\ProductStandardCost; 
/**
 * Class to handle all product object logic
 * @createdDate: 27-Jan-2020
 * @author Sopha Pum
 * @company REDFOX Web Solutions
 */
class ProductHandler
{
     

    /**
     * Method Create default pricebook entry for product with standard pricebook
     * it work if product has field
     * - std_unitprice
     * - std_regularprice
     * 
     * Check If product object has field above
     *  - If it has, 
     *      - get the standard pricebook to create/update pricebook entry
     *      - check if this product has pricebook entry that pricebook is standard
     *          - If has, update unitprice to that sale_price
     *          - If not, create new pricebook entry that from pricebook and sale_price
     *  - If it hasnot, do nothing
     * @param $lstProducts  array products to create/update pricebook
     * @return void
     */
    public static function createDefaultPbe($lstProducts){
 

        //- get the standard pricebook to create/update pricebook entry
        $lstpbs = Pricebook::where("is_standard", 1)->where("is_active", 1)->get()->toArray();

      

        //if there are no standard pricebook, we need to create a new one
        if(empty($lstpbs)){
            $pbController = new PricebookController();
            $newPb = [];
            $newPb["is_standard"] = 1;
            $newPb["is_active"] = 1;
            $newPb["name"] = "Standard";
            $lstpbs = $pbController->createLocal([$newPb]);
        }
        $pb = $lstpbs[0];
        

        //get all pricebook entry that use standard pricebook to check if there are existed product in there
        $lstProIds = [];
        foreach ($lstProducts as $index => $product) {
         
            if(!isset($product["std_unitprice"]) || $product["std_unitprice"] <= 0) continue;

            //put all product ids into a list to get pricebook entry for update/create
            $lstProIds[] = $product["id"];
        }
        
        
        $mapProPbes = [];
        if(!empty($lstProIds)){
            //get all standard pricebook entry that match product and pricebook
            $mapProPbes = PricebookEntry::whereIn("products_id", $lstProIds)
                            ->where("pricebook_id", $pb["id"])
                            ->get()
                            //we can map product as key because 1 product has only 1 active pricebook  
                            ->mapWithKeys(function($item){
                                return [$item['products_id'] => $item];
                            })
                            ->all();
        }

        
        // - check if this product has pricebook entry that pricebook is standard
        //     - If has, update unitprice to that sale_price
        //     - If not, create new pricebook entry that from pricebook and sale_price 
        $lstPbs2Create = [];
        $lstPbs2Update = [];
        foreach ($lstProducts as $index => $product) {
            if(!isset($product["std_unitprice"]) || $product["std_unitprice"] <= 0) continue;

            $proId = $product["id"];

            //check if this product has pricebook entry that pricebook is standard
            if(isset($mapProPbes[$proId])){ 
                $pbe = [
                    "id" => $mapProPbes[$proId]["id"],
                    "unit_price"=> $product["std_unitprice"],
                    "regular_price"=> isset($product["std_regularprice"]) ? $product["std_regularprice"] : 0

                ];
                $lstPbs2Update[] = $pbe;
            }else{
                $pbe = ["products_id" => $proId, 
                        "pricebook_id" => $pb["id"],
                        "is_default" => 1,
                        "unit_price" => $product["std_unitprice"],
                        "regular_price"=> isset($product["std_regularprice"]) ? $product["std_regularprice"] : 0
                    ];
                $lstPbs2Create[] = $pbe;
            }
        }
        
        $pbeCtrler = new PricebookEntryController();

        if(count($lstPbs2Create) > 0){
            $pbeCtrler->createLocal($lstPbs2Create);
        }

        
        if(count($lstPbs2Update) > 0){
            $pbeCtrler->updateLocal($lstPbs2Update);
        }
        
    }

    /**
     * Method to setup default fields for each product before create product
     * @param $lstNewProducts       binding list new products
     */
    public static function setupDefaultFieldOnCreate(&$lstNewProducts){
        
        foreach ($lstNewProducts as $key => &$product) {
            //generate code
            if(!isset($product["code"])){
                $product["code"] = DatabaseGW::generateReferenceCode("products");
            }
        }
    }

    /**
     * Method to create standard costing if there are costing value from product
     * @param $lstProducts      list new product that just created
     */
    public static function createDefaultCosting($lstProducts){

        $lstProdCost2Create = [];

        foreach ($lstProducts as $index => $product) {

            //if product cost_method is standard and it has costing value with product, we will
            //create costing for that product depend on field standard_cost
            if(isset($product["cost_method"]) && $product["cost_method"] == 'standard'){

                //In case it doesnt has field standard_cost, it means cost is 0
                $costing = isset($product["standard_cost"]) ? $product["standard_cost"] : 0;
                $effectiveDate = isset($product["cost_effective_date"]) ? $product["cost_effective_date"] : null;

                $newCost = [
                    "current_cost" => $costing,
                    "effective_date" => $effectiveDate,
                    "products_id" => $product["id"]
                ];
                $lstProdCost2Create[] = $newCost;
            }
        }

        if(!empty($lstProdCost2Create)){
            $costController = new ProductStandardCostController();
            $costController->createLocal($lstProdCost2Create);
        }
    }


    /**
     * Method to create new product costing when we update costing
     * This logic will work for product valuation_method = standard
     * @param $oldProduct       Old product record 
     * @param $newProduct       New product info
     * @return void
     * @author Sopha Pum
     */
    public static function createCostingOnUpdate($oldProduct, $newProduct){

        //if there are no standard_cost fields, we dont need apply this logic
        if(!isset($newProduct["standard_cost"])) return;
 
        $oldCost = isset($oldProduct["standard_cost"]) ? $oldProduct["standard_cost"]: 0;
        $newCost = isset($newProduct["standard_cost"]) ? $newProduct["standard_cost"]: 0;

        $valuationMethod = isset($newProduct["cost_method"]) ? $newProduct["cost_method"] : (isset($oldProduct["cost_method"]) ? $oldProduct["cost_method"] : 'standard');
        $newEffectiveDate = isset($newProduct["cost_effective_date"]) ? $newProduct["cost_effective_date"]: null;

        //get existed active cost (cost which is effective date greater than today or null)
        $lstExistedActiveCosts = ProductStandardCost::where("products_id", $newProduct["id"])
                                                    ->where("is_active", 1)
                                                    ->get()
                                                    ->toArray();
        
        //if old cost, isn't equal new cost, it means the cost is changing and 
        //we need to update existed cost to not active
        if($oldCost != $newCost && $valuationMethod == 'standard'){

            $costController = new ProductStandardCostController();

            //update all existed cost to inactive
            foreach ($lstExistedActiveCosts as $key => &$existedCost) {
                $existedCost["is_active"] = 0;
            }

            if(!empty($lstExistedActiveCosts)){
                $costController->updateLocal($lstExistedActiveCosts);
            }

            //create new product cost
            $newCost = [
                "current_cost" => $newCost,
                "effective_date" => $newEffectiveDate,
                "products_id" => $newProduct["id"]
            ];
            $lstProdCost2Create[] = $newCost;
            
            $costController->createLocal($lstProdCost2Create);

        }
    }

    /**
     * Method to create default Product GL Account by category
     * @param $product      product record that just created
     * @return void
     * @createdDate 03-07-2020
     * @author SP
     */
    private static function createDefaultProdGLAcc($product){
        if(isset($product["category_ids"])){

            //get Category GL Account to create GL Account for product 
            $lstCategoryGLAcc = GLAccMapping::where("obj_record_id", $product["category_ids"])
                                            ->where("is_active", 1)
                                            ->get()
                                            ->toArray(); 

            //get existed Product GL Account to compare which GL Account should we create
            $mapProdGLAcc = GLAccMapping::where("obj_record_id", $product["id"])
                                            ->where("is_active", 1)
                                            ->get()
                                            ->mapWithKeys(function($item){
                                                //active account type for each record 
                                                return [$item["acc_type"] => $item];
                                            })
                                            ->all();
            
            $lstProGLAcc2Create = [];

            foreach ($lstCategoryGLAcc as $index => $cateGLAcc) {

                //if there are no GL Acc for product, we will create a default for it by using Category GL Acc
                if(!isset($mapProdGLAcc[$cateGLAcc["acc_type"]])){
                    $lstProGLAcc2Create[] = [
                        "object_name" => "products", 
                        "obj_record_id" => $product["id"], 
                        "acc_type" => $cateGLAcc["acc_type"], 
                        "code" => $cateGLAcc["code"], 
                        "is_active" => 1, 
                        "chart_of_acc_id" => $cateGLAcc["chart_of_acc_id"]
                    ];
                }
            }

            if(count($lstProGLAcc2Create) > 0){
                $glAccController = new GLAccMappingController();
                $glAccController->createLocal($lstProGLAcc2Create);
            }

        }
    }

    public static function updateProductOptions($lstNewProducts, $mapOldProducts){

        foreach ($lstNewProducts as $key => $newProduct) {
            $oldProduct = $mapOldProducts[$newProduct["id"]];

            // if master product has change is_active, 
            // we need to update option product to the same master product
            if(isset($newProduct["for_sale"]) && $oldProduct["for_sale"] != $newProduct["for_sale"]){
                $itemType = isset($newProduct["item_type"]) ? $newProduct["item_type"] : $oldProduct["item_type"];

                if($itemType == "master"){
                    $lstProdOpts = Products::where("option_master_id", $newProduct["id"])->get()->toArray();

                    if(!empty($lstProdOpts)){
                        foreach ($lstProdOpts as $key => &$prodOpt) {
                            $prodOpt["for_sale"] = $newProduct["for_sale"];
                        }
                        $prodController = new ProductsController();
                        $prodController->updateLocal($lstProdOpts);
                    }
                }
            }
        }
    }
}
