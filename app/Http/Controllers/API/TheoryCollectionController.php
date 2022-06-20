<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\TheoryCollection;

class TheoryCollectionController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'theory_collection',
            'model' => 'App\Model\TheoryCollection', 
            'prefixId' => 't0c'
        ];
    }
    
    public function getQuery(){
        return TheoryCollection::query();
    }
    
    public function getModel(){
        return 'App\Model\TheoryCollection';
    } 
}
