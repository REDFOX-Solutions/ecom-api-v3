<?php

namespace App\Http\Controllers\API;
 
use App\Model\IssueProduct;
use App\Services\JournalEntryHandler;
use Illuminate\Http\Request;

class IssueProductController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'issue_product',
            'model' => 'App\Model\IssueProduct', 
            'prefixId' => '900',
            'modelTranslate' => 'App\Model\IssueProductTranslate',
            'prefixLangId' => '901',
            'parent_id' => 'issue_prod_id'
        ];
    }

    public function getQuery(){
        return IssueProduct::query();
    }
    
    public function getModel(){
        return 'App\Model\IssueProduct';
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

    public function afterCreate(&$lstNewRecords){
        foreach ($lstNewRecords as $key => $newIssueProd) {
            if(isset($newIssueProd["status"]) && $newIssueProd["status"] == 'completed'){

                //if issue change status to completed, we create Journal Entry
                JournalEntryHandler::createJEFromIssueProd($newIssueProd["id"]);
            }
        }
    }

    public function afterUpdate(&$lstNewRecords, $mapOldRecords = [])
    {
        
        foreach ($lstNewRecords as $key => $newIssueProd) {
            $oldIssueProd = $mapOldRecords[$newIssueProd["id"]];

            if(isset($newIssueProd["status"]) && 
                $oldIssueProd["status"] != $newIssueProd["status"] &&
                $newIssueProd["status"] == 'completed'){

                //if issue change status to completed, we create Journal Entry
                JournalEntryHandler::createJEFromIssueProd($newIssueProd["id"]);
            }
        }
    }
}
