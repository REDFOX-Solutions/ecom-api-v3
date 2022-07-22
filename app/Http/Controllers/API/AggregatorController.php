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

        $aggregator = Aggregator::whereDate('txn_date', '=', $txnDate)->first();

        if ($aggregator != null) {

            $readFile = Storage::disk('s3')->read($aggregator->file_name);

            $jsonContents = json_decode($readFile);

            array_push($jsonContents, $data);

            $json = json_encode($jsonContents);
            Storage::disk('s3')->update($aggregator->file_name, $json);

        } else {
            $filePath = $branch . '/' . $txnDate . '/' . Str::random(40) . '.json';

            $jsonContents = [$data];
            $json = json_encode($jsonContents);

            Storage::disk('s3')->put($filePath, $json);

            $aggregator = new Aggregator();
            $aggregator->fill([
                'id' => Str::random(),
                'file_name' => $filePath,
                'mime_type' => 'json',
                'txn_date' => $data['txn_date'],
            ]);

            $aggregator->save();
        }

        return response()->json($aggregator);
    }

    public function show($id)
    {
        $data = [];

        $aggregator = Aggregator::findOrFail($id);
        $readFile = Storage::disk('s3')->read($aggregator->file_name);
        $data['data'] = json_decode($readFile);

        return response()->json($data);
    }
}
