<?php

namespace App\Services;

use App\Http\Controllers\API\FloorTableController;
use App\Model\SaleOrder;

class FloorTableHandler
{
    /**
     * Method to check table available and update table
     * This method will invoke when Sale Order change status to open
     * If there are no active sale order is using table, we will update table status to active
     * @param $tableId      String table Id
     */
    public static function checkUpTableStatus($tableId){
        $numberActiveOrder = SaleOrder::where("floor_table_id", $tableId)
                                        ->where("status", 'hold')
                                        ->count();
 
        //if there are no active sale order is using table, this means table is available
        //if table is available, we will update table status to active
        $tableController = new FloorTableController();
        $table = [
            "id" => $tableId,
            "status" => ($numberActiveOrder > 0 ? "busy": "available")
        ];
        $tableController->updateLocal([$table]);
    }

}
