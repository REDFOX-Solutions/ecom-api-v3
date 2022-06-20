<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Exceptions\CustomException;
use App\Http\Controllers\API\MetadataConfigController;
use App\Http\Controllers\API\ReferenceCodeController;
use App\Http\Resources\RestResource;
use App\Model\AvailableLanguages;
use App\Model\MetaDataConfig;
use App\Model\ReferenceCode;
use App\Model\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;

class DatabaseGW
{
    /**
     * Method to query dynamic record
     * 
     * prefix for where condition
     * - "whereraw_fieldname" it means search value by find_in_set
     * - "not__fieldname" it mean search value not equals to value
     * - "g__fieldname" it means search with condition greater than
     * - "ge__fieldname" it means search with condition greater than or equals
     * - "l__fieldname" it means search with condition less than
     * - "le__fieldname" it means search with condition less than or equals
     * - "year__fieldname" it means search record in year by fieldname
     * - "month__fieldname" it means search record in month by fieldname
     * - "date__fieldname" it means search record in date equal fieldname
     * - "dg__fieldname" it means search record in date greater than by fieldname
     * - "dge__fieldname" it means search record in date greater than or equals by fieldname
     * - "diff__fieldname" it means search record in  between dates | e.g. diff__fielddate : "2021-08-01,2021-08-30"
     * - "dl__fieldname" it means search record in date less than by fieldname
     * - "dle__fieldname" it means search record in date less than or equals by fieldname
     * - "kw__fieldname" it means search record in keyword by fieldname
     * - "with" it means get data with relationship
     * - "offset" to skip record
     * - "limit" to limit record. default 50 record
     * @param App\Model\ModelName $modelString   Model String path
     * @param array $lstFilters array to filter query
     * @return array result query records 
     */
    public static function queryByModel($model, $lstFilters = [])
    {

        $withObject = [];
        $withCountObj = [];
        $limit = 50;
        $offset = 0;
        $orderCol = "created_date";
        $orderby = "desc";

        //added filter query before get data
        foreach ($lstFilters as $colName => $value) {

            //get all additional objects present in model
            if ($colName == "with" && !empty($value)) {
                $withObject = explode(",", $value);
                continue;
            }

            //this is not column for table
            if ($colName == "model_name") { 
                continue;
            }

            //get all with count obj
            if (($colName == "with_count" || $colName == "withCount") && !empty($value)) {
                $withCountObj = explode(",", $value);
                continue;
            }

            if ($colName == 'page') continue;


            if ($colName == 'order_by' || $colName == 'orderBy') { // 'order_by' : 'asc' | 'desc'
                $orderby = $value;
                continue;
            }

            if ($colName == 'order_col' || $colName == 'orderCol') { // 'order_col' : 'ordering'
                $orderCol = $value;
                continue;
            }

            if ($colName == 'offset') {
                $offset = $value;
                continue;
            }

            if ($colName == 'limit') {
                $limit = $value;
                continue;
            }

            //to check if column need check condition with whereraw
            //the request will has prefix "whereraw_"
            if (strpos($colName, 'whereraw') !== false) {

                if (!isset($value) || empty($value)) {
                    continue;
                }
                $colName = str_replace("whereraw_", "", $colName);

                if (strpos($value, ",") === false) {
                    $model->whereRaw("find_in_set('" . $value . "', $colName)");
                } else {
                    $lstVal = explode(",", $value);

                    foreach ($lstVal as $index => $val) {
                        $model->orWhereRaw("find_in_set('" . $val . "', $colName)");
                    }
                }
                continue;
            }

            if (strpos($colName, 'not__') !== false) {
                $colName = str_replace("not__", "", $colName);
                $model->where($colName, '!=', $value);
                continue;
            }

            //for query by checking if relationship existence
            if (strpos($colName, 'has__') !== false) {
                $colName = str_replace("has__", "", $colName);
                $model->has($colName, '>', 0);
                continue;
            }

            //for query year
            if (strpos($colName, 'year__') !== false) {
                $colName = str_replace("year__", "", $colName);
                $model->whereYear($colName, $value);
                continue;
            }

            //for query month
            if (strpos($colName, 'month__') !== false) {
                $colName = str_replace("month__", "", $colName);
                $model->whereMonth($colName, $value);
                continue;
            }
            //for query month  greater than or equals
            if (strpos($colName, 'mg__') !== false) {
                $colName = str_replace("mg__", "", $colName);
                $model->whereMonth($colName, '>=', $value);
                continue;
            }
            //for query date equals
            if (strpos($colName, 'date__') !== false) {
                $colName = str_replace("date__", "", $colName);
                $model->whereDate($colName, $value);
                continue;
            }
            //for query between date 
            if (strpos($colName, 'diff__') !== false) { 
                $colName = str_replace("diff__", "", $colName);
                $lstDates = explode(',', $value);
                $sDate = $lstDates[0];
                $eDate = $lstDates[1];


                //  $model->whereBetween($colName, [$sDate, $eDate]);
                $model->whereDate($colName, '>=', $sDate);
                $model->whereDate($colName, '<=', $eDate);
                // new Carbon();

                continue;
            }
            //for query date greater than
            if (strpos($colName, 'dg__') !== false) {
                $colName = str_replace("dg__", "", $colName);
                $model->whereDate($colName, '>', $value);
                continue;
            }

            //for query date greater than or equals
            if (strpos($colName, 'dge__') !== false) {
                $colName = str_replace("dge__", "", $colName);
                $model->whereDate($colName, '>=', $value);
                continue;
            }

            //for query date less than
            if (strpos($colName, 'dl__') !== false) {
                $colName = str_replace("dl__", "", $colName);
                $model->whereDate($colName, '<', $value);
                continue;
            }

            //for query date less than or equals
            if (strpos($colName, 'dle__') !== false) {
                $colName = str_replace("dle__", "", $colName);
                $model->whereDate($colName, '<=', $value);
                continue;
            }
            if (strpos($colName, 'kw__') !== false) {
                $colName = str_replace("kw__", "", $colName);
                $model->where($colName, 'like', '%' . $value . '%')->get();
                continue;
            }

            //if value is string null, we change it to use null variable
            if (!isset($value) || $value == 'null') {
                $model->where($colName, null);
            } else
                //if there are no comma in value, it means we need to search by whole value
                if (strpos($value, ",") === false) {
                    //check with condition greater than
                    if (strpos($colName, 'g__') !== false) {
                        $colName = str_replace("g__", "", $colName);
                        $model->where($colName, '>', $value);
                    } else
                        //check with condition greater  than or equals
                        if (strpos($colName, 'ge__') !== false) {
                            $colName = str_replace("ge__", "", $colName);
                            $model->where($colName, '>=', $value);
                        } else
                            //check with condition less than
                            if (strpos($colName, "l__") !== false) {
                                $colName = str_replace("l__", "", $colName);
                                $model->where($colName, '<', $value);
                            } else
                                //check with condition less than or equals
                                if (strpos($colName, "l__") !== false) {
                                    $colName = str_replace("le__", "", $colName);
                                    $model->where($colName, '<=', $value);
                                }
                                //default condition equals
                                else {
                                    $model->where($colName, $value);
                                }
                }
                //if there are more than one value, we switch to use whereIn
                //comma identify that it has multiple value
                else {
                    $lstVal = explode(",", $value);
                    $model->whereIn($colName, $lstVal);
                }
        }

        //user role filter
        if (isset(Auth::user()->user_roles_id) && !empty(Auth::user()->user_roles_id)) {
            $roleId = Auth::user()->user_roles_id;
            //get all roles below this current user role

            $lstRoleIds = UserRoleHandler::getAllBelowRole([$roleId]);

            $lstUsrIds = User::whereIn("user_roles_id", $lstRoleIds)
                ->get()
                ->keyBy('id')
                ->keys()
                ->all();
            $model->whereIn("created_by_id", $lstUsrIds);
        }

        if (!empty($withObject)) {
            foreach ($withObject as $index => $withName) {
                $model->with($withName);
            }
        }

        if (!empty($withCountObj)) {
            foreach ($withCountObj as $index => $withName) {
                $model->withCount($withName);
            }
        }

        $model->orderBy($orderCol, $orderby);
        $model->offset($offset);

        $lstResults = $model->paginate($limit)->appends(Request::except('page'));
        return $lstResults;
    }

    /**
     * Method to update or create a records
     * @param object $record    record to be create/update
     * @param array $config     the table config setting
     * @param int $key          key to merge with id to advoid duplicate
     * @param boolean $isAnonymous|false    option to create/update without auth
     */
    public static function updateOrCreate($record, $config, $key = 0, $isAnonymous = false)
    {
        $resObj = []; 
        
        if (!empty($config) && !empty($record)) {
            $recordId = "";

            DB::beginTransaction();
            try {

                // get dynamic model form config
                $className = $config['model'];
                $Model = new $className;
                $mapLangsData = [];

                // if langs empty not create and update translation
                if (isset($record['langs'])) {
                    $mapLangsData = $record['langs'];
                    unset($record['langs']);
                }

                $isUpdate = isset($record['id']) && !empty($record['id']);

                // set system fields to record
                self::generateSysFields($record, $isUpdate, $isAnonymous);

                // generate record id for insert
                $record['id'] = $isUpdate ? $record['id'] : self::generateId($config['prefixId'] . $key);

                //if it is update transaction, we will do all check permission and data owner
                // if ($isUpdate) {

                //     //get record from database to check if there are existed record in database
                //     $recordDB = $Model::where('id', $record['id'])->first();

                //     //if there are no record in database, return error
                //     if (!$recordDB) {
                //         throw new CustomException("Invalid Record!", 404);
                //     }

                // }

                // if record have id, update record
                // if record have not id, generate id and create
                $Model::updateOrCreate(["id" => $record['id']], $record);
                $recordId = $record['id'];

                // to create translate record
                // if config no modelTranslate , not create record,
                if (isset($config['modelTranslate'])) {
                    self::createLang($record['id'], 
                                    $config['parent_id'], 
                                    $config['prefixLangId'], 
                                    $config['modelTranslate'], 
                                    $mapLangsData, 
                                    $isAnonymous);

                }

                //create children here
                if(isset($Model::$relationship["children"])){
                    self::upsertChildren($record, $Model::$relationship["children"]);
                }

                DB::commit();

                //get record back 
                $lstExistedRecord = $Model::query()->where("id", $recordId)->get()->toArray();

                //$Model::query()->findOrFail($recordId);//not select back because we need to keep field requested from client
                $resObj = (count($lstExistedRecord) > 0) ? array_merge($record, $lstExistedRecord[0]) : $record;
 
            } catch (QueryException $e) {
                DB::rollback();

                $msgError = $e->getMessage();

                if(isset($e->errorInfo) && count($e->errorInfo) > 0){
                    $msgError = $e->errorInfo[2];
                } 
                throw new CustomException($msgError, $e->getCode(), []);
 
            } catch (Exception $e) {
                DB::rollback();
                throw new CustomException($e->getMessage(), $e->getCode(), []);
            }
        }

        return $resObj;
    }

    /**
     * Method to upsert each children assigned from request
     * NOTE: we need to setup children that we want auto upsert in each Model
     * @param $record           Referent object record
     * @param $lstChildren      List config children in model
     * @return void
     */
    public static function upsertChildren(&$record, $lstChildren){
        if(isset($lstChildren) && count($lstChildren) > 0){
            $lstCreateRecords = [];
            $lstUpdateRecords = [];

            foreach ($lstChildren as $index => $child) { 

                //name, parent_field, controller are required to create children
                if(isset($child["name"]) && 
                    isset($child["parent_field"]) && 
                    isset($child["controller"]))
                {
                    $relationName = $child["name"];
                    $parentFieldName = $child["parent_field"];
                    $controller = $child["controller"];

                    //to make sure requested record has children to create
                    //if there are no relation with record, we do nothing
                    if(isset($record[$relationName])){
                        $lstChildRecords = $record[$relationName];

                        //fetch children record for upsert
                        foreach ($lstChildRecords as $ind => $childRecord) {
                            $childRecord[$parentFieldName] = $record["id"]; //always assign parent id for children

                            //if child record has id, it means this is for update record
                            if(isset($childRecord["id"]) && !empty($childRecord["id"]))
                            {
                                $lstUpdateRecords[] = $childRecord;
                                continue;
                            }
                            $lstCreateRecords[] = $childRecord;
                            //case child record is create
                        }

                        $lstCreated = [];
                        $lstUpdated = [];
                        $modelController = new $controller();
                        if(count($lstCreateRecords) > 0){
                            $lstCreated = $modelController->createLocal($lstCreateRecords);
                        }
                        if(count($lstUpdateRecords) > 0){
                            $lstUpdated = $modelController->updateLocal($lstUpdateRecords);
                        }
                        //we put back the upserted record to record because we will use it later
                        $record[$relationName] = array_merge($lstCreated, $lstUpdated);
                    }
                    
                }
            }
        }
    }

    /**
     * Method to auto populate system fields
     */
    public static function generateSysFields(&$record, $isUpdate = false, $isAnonymous = false)
    {

        $ownerid = "Anonymous";
        if (!$isAnonymous && Auth::check()) {
            $ownerid = Auth::user()->id;
        }

        $record['updated_by_id'] = $ownerid;
        if (!$isUpdate) {
            $record['created_by_id'] = $ownerid;
        }

        //system fields that we don't allow user to manually input
        unset($record['updated_date']);
        unset($record['created_date']);
    }


    public static function generateId($preFixId)
    {
        //   list($usec, $sec) = explode(" ", microtime());
        //$milliseconds = round(((float)$usec + (float)$sec) * 1000);

        //   $milliseconds = round(microtime(true) * 1000);
        //  return $preFixId.$milliseconds;
        return uniqid($preFixId);
    }

    /**
     * Method to generate Auto Number by getting from DB
     * @param $uniqueName       String name for generate
     * @param $prefix           Optional, prefix code
     * @param $length           Optional, default 4, length code that we will return
     * @return $code generated
     * @author Sopha Pum
     */
    public static function generateAutoCode($uniqueName, $prefix="", $length=4){
        //get code from metaconfig
        $lstMetaConfigs = MetaDataConfig::where("name", $uniqueName)
                                        ->where("company_id", Auth::user()->company_id)
                                        ->get()
                                        ->toArray();

        $metaCtrl = new MetadataConfigController();
        //if there are no match a unique name, we will create a new one
        if(empty($lstMetaConfigs)){
            $newConfig = [
                "name" => $uniqueName, 
                "value" => "1", 
                "company_id" => Auth::user()->company_id
            ];
            $lstMetaConfigs = $metaCtrl->createLocal([$newConfig]);
        }
 
        $currentNum = isset($lstMetaConfigs[0]["value"]) ? intval($lstMetaConfigs[0]["value"]) : 1;
        $currentNum = $currentNum + 1;

        $lstMetaConfigs[0]["value"] = $currentNum;
        $metaCtrl->updateLocal($lstMetaConfigs);
        
        return $prefix . (str_pad($currentNum, $length , "0", STR_PAD_LEFT));

    }


    /**
     * Method generate reference code by return current code and save new code for next request
     * reference code will has prefix => object name + prefix e.g. receipt_prefix
     * reference code can have subfix => object name + subfix e.g. receipt_subfix 
     * reference code will has code length => object name + code_length e.g. receipt_code_length
     * reference code will has current number => object name + current_num e.g. receipt_current_ref_num (this record will auto change)
     * 
     * options
     * - prefix                 : Word before number
     * - subfix                 : Word after number
     * - current_ref_num        : start number 
     * - code_length            : length number
     * 
     * usage:
     * store in database by: Table_Name + '_' + Option above
     * 
     * e.g. Receipt reference code: rec2000001test, rec2000002test
     * => record receipt in meta config will be 
     * - receipt_prefix = rec20
     * - receipt_subfix = test
     * - receipt_current_ref_num = 1
     * - receipt_code_length = 00000
     * => receipt in DB will be: rec20 00001 test
     * 
     * 
     * @param $objName   String database table name
     * @return string current value of reference code
     * @author Sopha Pum
     */
    public static function generateReferenceCode($objName)
    {
        $mapConfigs = ReferenceCode::get()
            ->keyBy('name')
            ->all();

        $prefixKey = $objName . '_prefix';//GL00000000
        $subfixKey = $objName . '_subfix';
        $codeLengthKey = $objName . '_code_length';//default 5 : 0000000000
        $curNumKey = $objName . '_current_ref_num';

        $prefix = (isset($mapConfigs[$prefixKey]) && isset($mapConfigs[$prefixKey]["value"]) ? $mapConfigs[$prefixKey]["value"] : "");
        $subfix = (isset($mapConfigs[$subfixKey]) && isset($mapConfigs[$subfixKey]["value"]) ? $mapConfigs[$subfixKey]["value"] : "");
        $codeLength = (isset($mapConfigs[$codeLengthKey]) && isset($mapConfigs[$codeLengthKey]["value"]) ? intval($mapConfigs[$codeLengthKey]["value"]) : 5); //default 5: 00000
        $currentNum = (isset($mapConfigs[$curNumKey]) && isset($mapConfigs[$curNumKey]["value"]) ? intval($mapConfigs[$curNumKey]["value"]) : 1);

        //do update current number
        $newCurrentNum = [
            "name" => $objName . '_current_ref_num',
            "value" => "1"
        ];

        $existedMeta = [];
        $existedMeta = (isset($mapConfigs[$curNumKey]) ? collect($mapConfigs[$curNumKey])->toArray() : $newCurrentNum);
        $existedMeta["value"] = intval($existedMeta["value"]) + 1; //increase 1 for current number


        $metaConfigController = new ReferenceCodeController();
        $metaConfigController->upsertLocal([$existedMeta]); //cast to array because to avoid object given

        return $prefix . (str_pad($currentNum, $codeLength, "0", STR_PAD_LEFT)) . $subfix;
    }


    /**
     * Method to return list as RestResource
     * @param $model        Object Model
     * @param $lstRecords   array records that want to collect
     */
    public static function returnRestResource($model, $lstRecords)
    {
        $lstIds = collect($lstRecords)->keyBy('id')->keys()->all();
        return RestResource::collection(self::queryByModel($model, ["id" => implode(",", $lstIds), "limit" => (count($lstIds) + 1)]));
    }

    /**
     * Method Create Language for record by create all available language
     * @param $parentId             String parent id, 
     * @param $parentField          String parent field, 
     * @param $prefixId             String prefix id for record translate, 
     * @param $classTranslate       String Path to class translate, 
     * @param $mapNewLangData       Array for new lang data, 
     * @param $isAnonymous=false    to know if action is from Anounymous
     * @return void
     * @author Sopha Pum | 23-07-2021
     */
    public static function createLang($parentId, $parentField, $prefixId, $classTranslate, $mapNewLangData, $isAnonymous=false){ 
        $ModelTranslate = new $classTranslate;

        $lstAvailableLangs = AvailableLanguages::get()
                                                ->toArray();

        $mapExistedLangs = $ModelTranslate::where($parentField, $parentId)
                                            ->get()
                                            ->keyBy("lang_code")
                                            ->all();

        //to Avoid error lang, we need to get record back first

        foreach ($lstAvailableLangs as $index => $lang) {
            $langCode = $lang["lang_code"]; 

            //if map language has data from request, we get data to store in DB
            if(isset($mapNewLangData[$langCode])){
                
                $recordTranslate = $mapNewLangData[$langCode];
                $isUpdateTranslate = isset($recordTranslate['id']) && !is_null($recordTranslate['id']);

                $recordTranslate['lang_code'] = $langCode;
                $recordTranslate[$parentField] = $parentId;
                $recordTranslate['id'] = $isUpdateTranslate ? $recordTranslate['id'] : self::generateId($prefixId);
                self::generateSysFields($recordTranslate, $isUpdateTranslate, $isAnonymous);
                $ModelTranslate::updateOrCreate(["id" => $recordTranslate['id']], $recordTranslate); 

            }
            //if there are no language with request and there are no existed in DB, 
            //we will create a default once
            else if(!isset($mapExistedLangs[$langCode])){
                $defaultTranslate = [
                    "id" => self::generateId($prefixId),
                    "lang_code" => $langCode,
                    "$parentField" => $parentId,
                ];
                self::generateSysFields($defaultTranslate, false, $isAnonymous);
                $ModelTranslate::updateOrCreate(["id" => $defaultTranslate['id']], $defaultTranslate);  
            }
        }
    }
}
