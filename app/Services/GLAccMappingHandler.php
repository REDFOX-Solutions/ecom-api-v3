<?php

namespace App\Services;

use App\Http\Controllers\API\GLAccMappingController;

class GLAccMappingHandler
{
    /**
     * Method to update/create product GL Account when product created/updated
     * @param $product      Product record
     * @return void
     * @createdDate 03-07-2020
     * @author SP
     */
    public static function upsertProductGLAccount($product){

        //we need to check if there are attached gl_account with product or not to avoid error
        if(isset($product["gl_accounts"])){
            $lstGLAccounts = $product["gl_accounts"];
            $lstCreateGLAccs = [];
            $lstUpdateGLAccs = [];

            foreach ($lstGLAccounts as $index => $glAcc) {
                $glAcc["object_name"] = "products";
                $glAcc["obj_record_id"] = $product["id"];
                $glAcc["is_active"] = 1;

                if(isset($glAcc["id"])){
                    $lstUpdateGLAccs[] = $glAcc;
                    continue;
                }
                $lstCreateGLAccs[] = $glAcc;
            }

            $glAccController = new GLAccMappingController();
            if(!empty($lstCreateGLAccs)){
                $glAccController->createLocal($lstCreateGLAccs);
            }

            if(!empty($lstUpdateGLAccs)){
                $glAccController->updateLocal($lstUpdateGLAccs);
            }
        }
    }
}
