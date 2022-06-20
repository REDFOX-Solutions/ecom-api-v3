<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestResource;
use App\Model\Document;
use App\Services\DatabaseGW;
use Illuminate\Http\Request;

class DocumentController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "document",
            "model" => "App\Model\Document",
            "modelTranslate" => "App\Model\DocumentTranslate",
            "prefixId" => "069",
            "prefixLangId" => "069T",
            "parent_id" => "document_id"
        ];
    }

    public function getQuery(){
        return Document::query();
    }

    public function getModel(){
        return "App\Model\Document";
    }
    
    public function getCreateRules(){
        return [];
    }

    public function getUpdateRules(){
        return [
            "id" => "required"
        ];
    } 

    public function publicIndex(Request $req){
        try{
            // return [];
            $lstFilter = $req->all(); 
            $lstFilter['is_public'] = 1;
            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $lstFilter));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }
}
