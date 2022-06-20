<?php

namespace App\Http\Controllers\API;

use App\Services\DataConvertionClass;
use Validator;
use Illuminate\Http\Request;
use App\Exceptions\CustomException; 
use Illuminate\Support\Collection;
use App\Http\Controllers\TriggerHandler;
use App\Http\Resources\RestResource;
use App\Services\DatabaseGW;
use App\Services\Helper;

abstract class RestAPI extends TriggerHandler
{
    protected abstract function getQuery();
    protected abstract function getModel();
    protected abstract function getTableSetting();

    /**
     * Method to check input value before insert/update
     * @param array $lstRecords list object records or object record as array
     * @param array $rules      array rules to check
     * @param array $customMsg  array custom message
     */
    public function validation($lstRecords, $rules=[], $customMsg=[]){
        if (empty($rules)) return;
 
        foreach ($lstRecords as $index => $record) {
            $validate = Validator::make($record, $rules, $customMsg);
            if ($validate->fails()) {
                throw new CustomException($validate->errors()->first(), 
                                            CustomException::$INVALID_FIELD, 
                                            $validate->errors()); 
            }
        }
    }
    
    /** Function that need to override in controller */
    public $noAuth = false;

    /** 
     * Function to get validation rule before create
     * @return array list of rules
     */
    public function getCreateRules(){
        return [];
    }

    /**
     * Function to get validation rule before update
     * @return array list of rules
     */
    public function getUpdateRules(){
        return [
            "id" => "required"
        ];
    }
 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        try{
            $model = $this->getQuery();
            $lstRecords = DatabaseGW::queryByModel($model, $request->all());
            return RestResource::collection($lstRecords);

        }catch(QueryException $ex){
            return $this->queryRespondError($ex);
        }catch(\Exception $ex){ 
            return $this->respondError($ex);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $model = $this->getModel();
            $record = $model::findOrFail($id);

            //return single record as resource
            return new RestResource($record);
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    /**
     * Store many newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request 
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        try{ 
 
            $lstRequestData = $request->all();   
            $lstRecords = $this->createLocal($lstRequestData);
            // return RestResource::collection($lstRecords);

 
            return new RestResource(collect($lstRecords));
            
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    public function createLocal($lstRecords){
        //run logic before creation, the method is depend on each controller
        $this->beforeCreate($lstRecords); 

        $this->validation($lstRecords, $this->getCreateRules());//validation before make action 

        $lstRecordCreated = $this->upsert($lstRecords);//login to create record goes here

        //run any logic after creation, the method is depend on each controller
        $this->afterCreate($lstRecordCreated);

        //get those record from database back because after upsert it is data from request
        // $lstIds = [];
        // foreach ($lstRecordCreated as $index => $record) {
        //     $lstIds[] = $record["id"];
        // }
        // if(!empty($lstIds)){
        //     $model = $this->getQuery();
        //     $lstRecords = $model->whereIn("id", $lstIds)->get()->toArray();// DatabaseGW::queryByModel($model, ["id"=> implode(",", $lstIds), "limit" => (count($lstIds) + 1)]);
        //     return $lstRecords;
        // } 
        return $lstRecordCreated; 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $record = $request->all();
            $record["id"] = $id;
            $lstRequestData = [$record];

            $lstReturn = $this->updateLocal($lstRequestData);  
            // return RestResource::collection($lstReturn); 
            return new RestResource(collect($lstReturn));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    /**
     * Method to handle bulk update record
     */
    public function updates(Request $request){
        try{ 
            $lstRequestData = $request->all();

            $lstReturn = $this->updateLocal($lstRequestData);
            // return RestResource::collection($lstReturn); 
            return new RestResource(collect($lstReturn));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    public function updateLocal($lstRecords){

        //get all editing record ids
        $recordIds = [];
        foreach ($lstRecords as $key => $record) {
            if(!empty($record['id'])){
                $recordIds[] = $record["id"]; 
            }
        }

        //get old records to do other action in trigger
        $model = $this->getModel();
        $lstOldRecords = $model::whereIn("id", $recordIds)->get();

        //convert list old record into map key=id, value=record
        $collectionOldRecord = $lstOldRecords->mapWithKeys(function($item){
            return [$item['id'] => $item];
        });
        $mapOldRecords = $collectionOldRecord->all();
    
        //run logic before creation, the method is depend on each controller
        $this->beforeUpdate($lstRecords, $mapOldRecords); 

        $this->validation($lstRecords, $this->getUpdateRules());//validation before make action 

        $lstRecordCreated = $this->upsert($lstRecords);//login to create record goes here
 
        //run any logic after creation, the method is depend on each controller
        $this->afterUpdate($lstRecordCreated, $mapOldRecords);

        //get those record from database back because after upsert it is data from request
        // $lstIds = [];
        // foreach ($lstRecordCreated as $index => $record) {
        //     $lstIds[] = $record["id"];
        // }
        // if(!empty($lstIds)){
        //     $model = $this->getQuery();
        //     $lstRecords = DatabaseGW::queryByModel($model, ["id"=> implode(",", $lstIds), "limit" => (count($lstIds) + 1)]);
        //     return $lstRecords;
        // } 
        if(!isset($lstRecordCreated) || count($lstRecordCreated) == 0) return [];
        return $lstRecordCreated; 
    }

    public function upsert(&$lstRecords){ 
        $lstRecordUpserted = [];
        $tableConfig = $this->getTableSetting();
        
        if(!empty($tableConfig) && !empty($lstRecords)){
            foreach ($lstRecords as $index => $record) { 
                $lstRecordUpserted[] = DatabaseGW::updateOrCreate($record, $tableConfig, $index, $this->noAuth);
            }
        }

        $lstFilterNull = array_filter($lstRecordUpserted, function($var){
            return isset($var) && !empty($var);
        });
        return $lstFilterNull;
    }

    public function upsertLocal($lstRecords){
        $lstCreateDatas = [];
        $lstUpdateDatas = [];
        foreach ($lstRecords as $index => $data) {
            if(isset($data["id"])){
                $lstUpdateDatas[] = $data;
                continue;
            }

            $lstCreateDatas[] = $data;
        }

        $lstCreateResult = [];
        $lstUpdateResult = [];

        

        if(!empty($lstUpdateDatas)){
            $lstUpdateResult = $this->updateLocal($lstUpdateDatas);
        }
        if(!empty($lstCreateDatas)){
            $lstCreateResult = $this->createLocal($lstCreateDatas);
        }
        $lstMerged = array_merge($lstCreateResult, $lstUpdateResult);

        $lstIds = [];
        foreach ($lstMerged as $index => $record) {
            $lstIds[] = $record["id"];
        }
        if(!empty($lstMerged)){
            return $lstMerged;
        } 
        return []; 
    }

    /**
     * Method to upsert record from api
     */
    public function apiUpsert(Request $request){
        try{ 
            $lstRequestData = $request->all();
            $lstReturn = $this->upsertLocal($lstRequestData);

            return new RestResource(collect($lstReturn)); 
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }


    public function delete($lstIds){
        try{ 
            if(empty($lstIds)) throw new CustomException('Invalid Record!', CustomException::$INVALID_RECORD, []);

            $model = $this->getModel();

            $lstOldRecords = $model::whereIn("id", $lstIds)->get()->toArray();

            $this->beforeDelete($lstOldRecords);   

            $record = $model::whereIn("id", $lstIds);
            $record->delete();

            $this->afterDelete($lstOldRecords); 
            return new RestResource(collect($lstOldRecords)); 

        }catch(\Exception $ex){
            throw new CustomException($ex->getMessage(), $ex->getCode());
        } 

        return [];

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $lstDeletedRecord = $this->delete([$id]);

            return $this->respondSuccess($lstDeletedRecord);
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    public function destroys(Request $request){ 
        try{
            $ids = $request->all(); 
            $lstDeletedRecord = $this->delete($ids);
 
            return $this->respondSuccess($lstDeletedRecord);
            // return $this->customException("Cannot Delete!");
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }
}
