<?php

namespace App\Services;

use App\Http\Controllers\API\PricebookController;
use App\Model\Pricebook;

class SalePriceHandler 
{

    /**
     * Method to get standard pricebook
     * Case no standard pricebook in system, we will create a new one
     * @return Standard Pricebook
     * @author Sopha Pum
     */
    public static function getDefaultPricebook(){
        $lstPbs = Pricebook::where("is_standard", 1)
                            ->where("is_active", 1)
                            ->get()
                            ->toArray();

        //if there are no standard pricebook, we will create a new one
        if(empty($lstPbs)){
            $newPb = [
                "is_standard" => 1,
                "is_active" => 1,
                "name" => "Standard"
            ];
            $pbCtrl = new PricebookController();
            $lstPbs = $pbCtrl->createLocal([$newPb]);
        }

        return $lstPbs[0];
    }
}
