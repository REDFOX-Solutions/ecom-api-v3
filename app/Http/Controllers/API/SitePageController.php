<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\RestResource;
use App\Model\SitePages;
use App\Services\DatabaseGW;
use Illuminate\Http\Request;

class SitePageController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'site_pages',
            'model' => 'App\Model\SitePages',
            'modelTranslate' => 'App\Model\SitePageTranslation',
            'prefixId' => 'siteP',
            'prefixLangId' => 'siteP0t',
            'parent_id' => 'site_pages_id'
        ];
    }
    
    public function getQuery(){
        return SitePages::query();
    }
    
    public function getModel(){
        return 'App\Model\SitePages';
    } 
    
    public function beforeCreate(&$lstNewRecords){
        # code logic here ...
    }
    
    public function afterCreate(&$lstNewRecords){ 
    }
    
    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){ 
    }
  
    public function publicIndex(Request $request){
        try{
            $filters = $request->all();  
            $filters['with'] = 'allSections';
            $model = $this->getQuery();
            $lstRecords = DatabaseGW::queryByModel($model, $filters);
 
            return RestResource::collection($lstRecords); 
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }
}
