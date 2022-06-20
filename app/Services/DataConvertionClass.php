<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class DataConvertionClass
{
    /**
     * Method to convert langs array to use lang code as key
     * @param array $lstRecords     list records that has langs in it
     * @param string $col_lang      lang code column name
     * @return array list of records that has langs with lang_code as key
     */
    public static function findLangs2ChangeIndex(&$lstRecords, $col_lang = "lang_code"){

        //to avoid lstRecords isn't array by accident
        $collection = collect($lstRecords);
        $lstRecords = $collection->toArray();//all nest object will convert to array

        foreach ($lstRecords as $index => &$record) {
            
            //if index is string, it means the current loop is object
            //if integer, it means the current loop is array object

            //in case the first loop is object
            if(gettype($index) == "string"){ 
                //in this case $index=column name, $record=column value
                self::convertLang($lstRecords, $col_lang);
                break;
            }
            //in case first loop is array object
            else if(gettype($index) == "integer"){
                self::convertLang($record, $col_lang);
            }
        }
    }

    /**
     * Method to change index langs in object record to use lang_code
     * @param {array} $object       a record as array by using colname as index, col value as value
     * @param {string} $lang_col    language code column name in DB
     * @return array of languages that has lang code as key
     */
    public static function convertLang(&$object, $lang_col = 'lang_code'){
        if(gettype($object) == 'array' || gettype($object) == 'object'){
            //to make sure the given var is array
            $collection = collect($object);
            $object = $collection->toArray();//all nest object will convert to array

            foreach ($object as $colname => &$value) {
                //find column langs to change index
                if($colname === 'langs' && gettype($value) == "array" && count($value) > 0){
                    $collectionLangs = collect($value);

                    $keyed = $collectionLangs->mapWithKeys(function ($item) use($lang_col) {
                            return array($item[$lang_col] => $item);
                    });

                    $value = $keyed->all();//all already return record with array
                }
                else if($colname === 'langs' && gettype($value) == "array" && empty($value)){
                    $value = (object)[];
                }

                //check if other column is array.
                //if it is array, need to find langs again
                else if($colname != 'langs' && gettype($value) == "array"){
                    self::findLangs2ChangeIndex($value);
                }
            }
        }
    }

    public static function mapAddress($lstAddresses){
        //to make sure the given var is array
        $collection = collect($lstAddresses);
        $lstAddresses = $collection->toArray();//all nest object will convert to array
        $lstTmp = [];

        foreach ($lstAddresses as $index => $address) {

            //if it has child, check its child to convert index
            if(isset($address["child_address"])){
                $address["child_address"] = self::mapAddress($address["child_address"]);
            }

            $lstTmp[$address["id"]] = $address;
        }

        return count($lstTmp) > 0 ? $lstTmp : null;
    }

    public static function mapParentAddress($lstAddresses){
        //to make sure the given var is array
        $collection = collect($lstAddresses); 
 
        $grouped = $collection->mapToGroups(function ($item, $key) {
            return [$item['parent_id'] => $item];
        });

        $lstGroupedAddress = $grouped->toArray();

        $mapGrouped = [];
        foreach ($lstGroupedAddress as $parentId => $lstChildren) {
            $collectionAddress = collect($lstChildren);
            $keyed = $collectionAddress->mapWithKeys(function ($item) {
                return [$item['id'] => $item];
            });
            $mapGrouped[$parentId] = $keyed->all();
        }

        return $mapGrouped;
    }
}
