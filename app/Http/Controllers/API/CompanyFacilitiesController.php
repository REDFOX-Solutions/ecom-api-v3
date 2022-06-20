<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\CompanyFacilities;
use Illuminate\Http\Request;

class CompanyFacilitiesController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "company_facilites",
            "model" => "App\Model\CompanyFacilities", 
            "prefixId" => "CF001"
        ];
    }

    public function getQuery(){
        return CompanyFacilities::query();
    }

    public function getModel(){
        return "App\Model\CompanyFacilities";
    }
}
