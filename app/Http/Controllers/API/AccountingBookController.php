<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Model\AccountingBook;

class AccountingBookController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'accounting_book',
            'model' => 'App\Model\AccountingBook',
            'prefixId' => 'acc0bk'
        ];
    }

    public function getQuery(){
        return AccountingBook::query();
    }

    public function getModel(){
        return 'App\Model\AccountingBook';
    }
}
