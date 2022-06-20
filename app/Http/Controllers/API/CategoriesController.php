<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Categories;
use App\Http\Resources\RestResource;
use App\Services\DatabaseGW;

class CategoriesController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'categories',
            'model' => 'App\Model\Categories',
            'modelTranslate' => 'App\Model\CategoryTranslation',
            'prefixId' => 'cate',
            'prefixLangId' => 'cate0t',
            'parent_id' => 'categories_id'
        ];
    }
    
    public function getQuery(){
        return Categories::query();
    }
    
    public function getModel(){
        return 'App\Model\Categories';
    }
    
    public function getCreateRules(){
        return [
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }  

    //guest access
    public function publicIndex(Request $request){
        try{
            $lstFilter = $request->all(); 
            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $lstFilter));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }
}
