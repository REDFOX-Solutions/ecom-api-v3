<?php

namespace App\Services;

use App\Http\Resources\RestResource;
use App\Model\Warehouse;
use Illuminate\Http\Request;

class WarehouseHandler
{

    private static $CONST_LOCATION_CONTROLLER = 'App\Http\Controllers\API\WarehouseLocationsController';
    private static $CONST_CONTACT_CONTROLLER = 'App\Http\Controllers\API\ContactController';

    private static $CONST_WAREHOUSE_MODEL = 'App\Model\Warehouse';

    private static $CONST_LOCATION = "locations";
    private static $CONST_CONTACT = "contacts";

    private static $CONST_CREATED_KEY = "created";
    private static $CONST_UPDATED_KEY = "updated";

    #region Handler afterCreate

    public static function createLocation(&$lstRecorded, $warehouse)
    {
        $childValues=[
            'warehouses_id'=> $warehouse['id'],//parent_id(fk) in child record
        ];
        self::create($lstRecorded, $warehouse, self::$CONST_LOCATION_CONTROLLER, self::$CONST_LOCATION, $childValues);
    }

    public static function createContact(&$lstRecorded, $warehouse)
    {
        $childValues = [
            'parent_id' => $warehouse['id'], //parent_id(fk) in child record
            'parent_type' => self::$CONST_WAREHOUSE_MODEL, //parent_type in child record
        ];
        self::create($lstRecorded, $warehouse, self::$CONST_CONTACT_CONTROLLER, self::$CONST_CONTACT, $childValues);
    }


    #endregion


    #region Handler afterUpdate

    public static function upsertLocation(&$lstRecorded, $warehouse) 
    {
        $childValues = [
            'warehouses_id' => $warehouse['id'], //parent_id(fk) in child record
        ];
        self::upsert($lstRecorded, $warehouse, self::$CONST_LOCATION_CONTROLLER, self::$CONST_LOCATION, $childValues);
    }

    public static function upsertContact(&$lstRecorded, $warehouse)
    {
        $childValues = [
            'parent_id' => $warehouse['id'], //parent_id(fk) in child record
            'parent_type' => self::$CONST_WAREHOUSE_MODEL, //parent_type in child record
        ];
        self::upsert($lstRecorded, $warehouse, self::$CONST_CONTACT_CONTROLLER, self::$CONST_CONTACT, $childValues);
    }

    #endregion






    /** child records must be define and array */
    public static function create(&$lstRecorded, $record, $controller, $childKey, $childValues)
    {
        $lstCreateChild = []; //array to store childs that create
        //check current record exist child or not

        if (isset($record[$childKey])) {
            if (is_array($record[$childKey])) {
                $childs = $record[$childKey];
                // Iterate childs
                foreach ($childs as $index => $child) {
                    //Iterate to fill some child column
                    foreach ($childValues as $key => $value) {
                        $child[$key] = $value;
                    }

                    $lstCreateChild[] = $child;
                }
            }
        }

        $childController = new $controller();

        if (count($lstCreateChild) > 0) {
            $tranRecorded = $childController->createLocal($lstCreateChild);
            $tranRecorded = $tranRecorded->toArray()['data']; //get inserted records

            //add record transaction to lstRecorded
            $lstRecorded[] = self::createRecordTransaction(
                self::$CONST_CREATED_KEY, //temporary save transaction
                $controller,              //record to rollback 
                $tranRecorded             //if accidentally error
            );
        }
    }

    /** child records must be define and array */
    public static function upsert(&$lstRecorded, $record, $controller, $childKey, $childValues)
    {
        $lstCreateChild = []; //array to store childs that create
        $lstUpdateChild = []; //array to store childs that update

        //check current record exist child or not
        if (isset($record[$childKey])) {
            if (is_array($record[$childKey])) {
                $childs = $record[$childKey];

                // Iterate childs
                foreach ($childs as $index => $child) {
                    //Iterate to fill some child column
                    foreach ($childValues as $key => $value) {
                        $child[$key] = $value;
                    }

                    // when child_id isset  
                    if (isset($child['id'])) {    //to update
                        $lstUpdateChild[] = $child;
                    } else { //to create
                        $lstCreateChild[] = $child;
                    }
                }
            }
        }

        $childController = new $controller();

        if (count($lstCreateChild) > 0) {

            $tranRecorded = $childController->createLocal($lstCreateChild);
            $tranRecorded = $tranRecorded->toArray()['data']; //get inserted records


            //add record transaction to lstRecorded
            $lstRecorded[] = self::createRecordTransaction(
                self::$CONST_CREATED_KEY, //temporary save transaction
                $controller,              //record to rollback 
                $tranRecorded              //if accidentally error
            );
        }

        if (count($lstUpdateChild) > 0) {

            //get old record
            $model = $childController->getModel();
            $lstOldRecord = $model::whereIn("id", array_column($lstUpdateChild, 'id'))->get();

            $childController->updateLocal($lstUpdateChild);

            //add record transaction to lstRecorded
            $lstRecorded[] = self::createRecordTransaction(
                self::$CONST_UPDATED_KEY, //temporary save transaction
                $controller,             //record to rollback 
                $lstOldRecord            //if accidentally error
            );
        }
    }




    #region RollBack
    public static function rollBack($lstRecorded)
    {
        foreach($lstRecorded as $record) {

            //new controller from record
            $controller= new $record['controller'];

            if($record['key'] == self::$CONST_CREATED_KEY) { //to destroy

                $toDestroys= array_column($record['values'], 'id');

                $request =new Request( $toDestroys);
                $controller->destroys($request);//get all id in old record from values to delete
            }
            else { //to restore
                $toRestore= $record['values'];//get old record from values to restore
                $controller->updateLocal($toRestore);

            }
        }

    }

    #endregion

    public static function createRecordTransaction($key, $controller, $records)
    { 
        $record['key'] = $key;
        $record['controller'] = $controller;
        $record['values'] = $records;

        return $record;
    }
}
