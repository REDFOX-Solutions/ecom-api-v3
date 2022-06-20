<?php

namespace App\Http\Controllers\API; 
 
use App\Model\MetaDataConfig;

class MetadataConfigController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "metadata_config",
            "model" => "App\Model\MetaDataConfig",
            "prefixId" => "mtd"
        ];
    }

    public function getQuery(){
        return MetaDataConfig::query();
    }

    public function getModel(){
        return "App\Model\MetaDataConfig";
    }
    
    public function getCreateRules(){
        return [ 
        ];
    }

    public function getUpdateRules(){
        return [ 
        ];
    }
 
}
