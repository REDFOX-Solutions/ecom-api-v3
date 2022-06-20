<?php

namespace App\Services;

use App\Model\RecordType;
use Illuminate\Database\Eloquent\Model;

class RecordTypeHandler
{
    public static function getRecordType($objName = ""){
        $query = RecordType::query();

        if($objName != ""){
            $query->where("object_name", $objName);
        }

        return $query->get()->keyBy("name")->all();
    }
}
