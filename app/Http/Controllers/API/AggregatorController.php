<?php

namespace App\Http\Controllers\API;


use App\Model\Aggregator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AggregatorController extends RestAPI
{

    //
    protected function getQuery()
    {
        return Aggregator::query();
    }

    protected function getModel()
    {
        return Aggregator::class;
    }

    protected function getTableSetting()
    {
        return [
            'tablename' => 'aggregators',
            'model' => 'App\Model\Aggregator',
            'prefixId' => 'ag',
        ];
    }

    public function getCreateRules()
    {
        return [
            "branch" => "required",
            "data.txn_date" => "required",
            "data.txn_no" => "required",
            "data.amount" => "required"
        ];
    }

    public function store(Request $request)
    {

        $request->validate([
            "branch" => "required",
            "data.txn_date" => "required",
            "data.txn_no" => "required",
            "data.amount" => "required"
        ]);

        $data = $request->data;
        $branch = $request->branch;

        $txnDate = Carbon::parse($data['txn_date'])->format('Y-m-d');

        $db_start_1 = hrtime(true);

        $aggregator = Aggregator::whereDate('txn_date', '=', $txnDate)->first();

        $db_end_1 = hrtime(true);

        $db_eta_1 = $db_end_1 - $db_start_1;

        if ($aggregator != null) {

            $s3_start = hrtime(true);

            $readFile = Storage::disk('s3')->read($aggregator->file_name);

            $jsonContents = json_decode($readFile);

            array_push($jsonContents, $data);

            $json = json_encode($jsonContents);
            Storage::disk('s3')->update($aggregator->file_name, $json);

            $s3_end = hrtime(true);

            $s3_eta = $s3_end - $s3_start;

            $aggregator['db_execute_time'] = $db_eta_1 / 1e+6;
            $aggregator['s3_execute_time'] = $s3_eta / 1e+6;

        } else {
            $filePath = $branch . '/' . $txnDate . '/' . Str::random(40) . '.json';

            $jsonContents = [$data];
            $json = json_encode($jsonContents);

            $s3_start = hrtime(true);

            Storage::disk('s3')->put($filePath, $json);

            $s3_end = hrtime(true);

            $s3_eta = $s3_end - $s3_start;

            $aggregator['s3_execute_time'] = $s3_eta / 1e+6;

            $aggregator = new Aggregator();
            $aggregator->fill([
                'id' => Str::random(),
                'file_name' => $filePath,
                'mime_type' => 'json',
                'txn_date' => $data['txn_date'],
            ]);

            $db_start_2 = hrtime(true);

            $aggregator->save();

            $db_end_2 = hrtime(true);

            $db_eta_2 = $db_end_2 - $db_start_2;

            $aggregator['db_execute_time'] = (($db_eta_1 + $db_eta_2) / 2) / 1e+6;

        }

        return response()->json($aggregator);
    }

    public function show($id)
    {
        $data = [];

        $db_start_2 = hrtime(true);

        $aggregator = Aggregator::findOrFail($id);

        $db_end_2 = hrtime(true);

        $db_eta_2 = $db_end_2 - $db_start_2;


        $s3_start_2 = hrtime(true);

        $readFile = Storage::disk('s3')->read($aggregator->file_name);

        $s3_end_2 = hrtime(true);

        $s3_eta_2 = $s3_end_2 - $s3_start_2;

        $data['s3_execute_time'] = $s3_eta_2 / 1e+6;
        $data['db_execute_time'] = $db_eta_2 / 1e+6;
        $data['data'] = json_decode($readFile);

        return response()->json($data);
    }
}
