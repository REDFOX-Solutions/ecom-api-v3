<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\PersonAccount;
use App\Http\Resources\RestResource;
use App\Services\DatabaseGW;
use App\Services\PersonAccountHandler;

class PersonAccountController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'person_account',
            'model' => 'App\Model\PersonAccount',
            'modelTranslate' => 'App\Model\PersonAccountTranslation',
            'prefixId' => 'acc',
            'prefixLangId' => 'acc0t',
            'parent_id' => 'person_account_id'
        ];
    }
    
    public function getQuery(){
        return PersonAccount::query();
    }
    
    public function getModel(){
        return 'App\Model\PersonAccount';
    }
    
    public function getCreateRules(){
        return [];
    }
    
    public function getUpdateRules(){
        return [];
    }
    
    //for guest
    public function publicIndex(Request $request){
        try{ 

            $model = $this->getQuery();
            $filters = $request->all();
            $filters['status'] = 'active';

            $lstRecords = DatabaseGW::queryByModel($model, $filters);
            return RestResource::collection($lstRecords);
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    public function publicStore(Request $req){ 
        $this->noAuth = true;
        return $this->store($req);
    }

    public function publicUpdates(Request $req){
        $this->noAuth = true;
        return $this->updates($req);
    }
    public function beforeCreate(&$lstNewRecords)
    {
        PersonAccountHandler::setupDefaultFieldOnCreate($lstNewRecords); 
    }
}
