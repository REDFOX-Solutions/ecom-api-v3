<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Model\SiteNav;
use App\Services\DatabaseGW;
use App\Http\Resources\RestResource; 
use App\Model\StaffShifts; 
use Carbon\Carbon;

class SiteNavController extends RestAPI
{
    public function getTableSetting()
    {
        return [
            'tablename' => 'site_nav',
            'model' => 'App\Model\SiteNav',
            'modelTranslate' => 'App\Model\SiteNavTranslation',
            'prefixId' => 'nav',
            'prefixLangId' => 'nav0t',
            'parent_id' => 'site_nav_id'
        ];
    }

    public function getQuery()
    {
        return SiteNav::query();
    }

    public function getModel()
    {
        return 'App\Model\SiteNav';
    }

    public function getCreateRules()
    {
        return [];
    }

    public function getUpdateRules()
    {
        return [
            'id' => 'required'
        ];
    }

    public function publicIndex(Request $request)
    {
        try {
            $model = $this->getQuery();
            $filters = [
                "with" => "subMenus",
                "site_nav_id" => null,
                "is_visible" => 1,
                "order_col" => "ordering",
                "order_by" => "asc"
            ];

            $lstRecords = DatabaseGW::queryByModel($model, $filters);
            return RestResource::collection($lstRecords);
        } catch (\Exception $ex) {
            return $this->respondError($ex);
        }
    }

    public function testing(Request $req)
    {
        $url = "http://testing.com/test/sadf";
        if(filter_var($url, FILTER_VALIDATE_URL)){
            echo "valid";
        }else{
            echo "invalid";
        }
        return " ";

        return DatabaseGW::generateReferenceCode("testing");
        $checkIn = Carbon::parse('2021-10-30');
        $checkOut = Carbon::parse('2021-11-06');

        // $nightsDays = $checkOut->diff($checkIn)->format('%a');
        $nightsDays = $checkIn->diffInDays($checkOut);

        return $nightsDays;

        $d = new Carbon('2020-06-26T09:32:16+0000');
        return $d->toDateString();
        $lstStaffShift = StaffShifts::whereNull("staff_end_day_id")
            ->whereDate("transaction_date", '2020-06-26')
            ->get()
            ->toArray();

        
        
        return $lstStaffShift; 
    }
}
