<?php

namespace App\Http\Controllers\API;

use App\Model\CustomerReservation;
use App\Services\CustomerReservationHandler;
use Illuminate\Http\Request;

class CustomerReservationController extends RestAPI
{
    //
    protected function getTableSetting()
    {
        return [
            'tablename' => 'customer_reservation',
            'model' => 'App\Model\CustomerReservation',
            'prefixId' => 'cus_re'
        ];
    }

    protected function getQuery()
    {
        return CustomerReservation::query();
    }

    protected function getModel()
    {
        return 'App\Model\CustomerReservation';
    }
    public function getCreateRules(){
        return [
            'customer_id'=>'required',
            'table_id' => 'required'
        ];
    }
    public function beforeCreate(&$lstNewRecords)
    {
        CustomerReservationHandler::checkExistedActiveReserve($lstNewRecords);
        CustomerReservationHandler::checkupCustomer($lstNewRecords);
        CustomerReservationHandler::setupDefaultFields($lstNewRecords);

    }
    public function afterCreate(&$lstNewRecords)
    {

    }
}
