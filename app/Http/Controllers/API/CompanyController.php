<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Company;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RestResource;
use App\Model\AvailableLanguages;
use App\Services\CompanyHandler;
use App\Services\DatabaseGW;

class CompanyController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'company',
            'model' => 'App\Model\Company',
            'modelTranslate' => 'App\Model\CompanyTranslation',
            'prefixId' => 'com',
            'prefixLangId' => 'com0t',
            'parent_id' => 'company_id'
        ];
    }
    
    
    public function getQuery(){
        return Company::query();
    }
    
    
    public function getModel(){
        return 'App\Model\Company';
    }
    
    public function getCreateRules(){
        return [
           "phone" => "required"
        ];
    }

    public function beforeCreate(&$lstNewRecords){
        CompanyHandler::setupFieldBeforeSave($lstNewRecords);
    }

    public function afterCreate(&$lstNewRecords)
    {
        //This method will create shop currency when has base currency
        CompanyHandler::createShopBaseCurrency($lstNewRecords);
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
        //This method will create shop currency when has base currency
        CompanyHandler::createShopBaseCurrencyAfterUpdate($lstNewRecords, $mapOldRecords);
    }

    public function getInfo(){
        $userInfo = Auth::user();
        return $this->show($userInfo["company_id"]); 
    }


    public function companyDomain(){ 
        $record = Company::query()->where("company_type" , "owner")->firstOrFail();
        //return single record as resource
        return new RestResource($record);
    }

    public function publicCompany(Request $req){
        // $record = Company::query()
        //             ->where("company_type" , "owner")
        //             ->with('availableLanguages')
        //             ->firstOrFail();
        // //return single record as resource
        // return new RestResource($record);

        try{ 
            $lstFilter = $req->all(); 
            $lstFilter['is_active'] = 1; 

            $with = 'availableLanguages';
            if(isset($lstFilter['with'])){
                $with .= ',' . $lstFilter['with'];
            }
            $lstFilter['with'] = $with;
            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $lstFilter));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        } 
    } 
    public function publicStore(Request $req){ 

        try{ 
            $lstFilter = $req->all(); 
            $lstFilter['is_active'] = 1;
            $lstFilter['record_type_name'] = "store";
            $lstFilter['with'] = "availableLanguages";
            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $lstFilter));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        } 
    }

    public function getAllConfig(){

        $userInfo = Auth::user();
        $company = Company::query()->where("id" , $userInfo["company_id"])->firstOrFail();
        $availableLang = AvailableLanguages::where("company_id", $company["id"])->get();

        $record = [
            "com_info" => $company,
            "available_langs" => $availableLang
        ];
        
        // return $record;
        return new RestResource(collect($record));
    }
}
