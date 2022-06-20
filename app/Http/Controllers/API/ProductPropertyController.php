<?php

namespace App\Http\Controllers\API;
 
use App\Model\ProductProperty;

class ProductPropertyController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'product_properties',
            'model' => 'App\Model\ProductProperty',
            'modelTranslate' => 'App\Model\ProductPropertyTranslation',
            'prefixId' => 'prodProp',
            'prefixLangId' => 'prodProp0t',
            'parent_id' => 'product_properties_id'
        ];
    }

    public function getQuery(){
        return ProductProperty::query();
    }
    
    public function getModel(){
        return "App\Model\ProductProperty";
    }
}
