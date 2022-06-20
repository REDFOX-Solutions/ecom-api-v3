<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\IssueProductDetail;
use Illuminate\Http\Request;

class IssueProductDetailController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'issue_product_detail',
            'model' => 'App\Model\IssueProductDetail', 
            'prefixId' => '910',
            'modelTranslate' => 'App\Model\IssueProductDetailTranslate',
            'prefixLangId' => '911',
            'parent_id' => 'issue_prod_detail_id'
        ];
    }

    public function getQuery(){
        return IssueProductDetail::query();
    }
    
    public function getModel(){
        return 'App\Model\IssueProductDetail';
    }
    
    public function getCreateRules(){
        return [ 
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }
}
