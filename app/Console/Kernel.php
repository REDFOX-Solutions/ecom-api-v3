<?php

namespace App\Console;

use App\Model\Aggregator;
use App\Model\Task;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        $tasks = Task::all();

        foreach ($tasks as $task) {
            $frequency = $task->frequency;
            $schedule->call(function () use ($task) {

                $txnDate = Carbon::now()->format('Y-m-d');
                $client = new Client();

                $response = $client->request('GET', 'https://jsonplaceholder.typicode.com/posts');

                $filePath = 'parkcafe' . '/' . $txnDate . '/' . Str::random(40) . '.json';

                Storage::disk('s3')->put($filePath, $response->getBody());

                $aggregator = new Aggregator();
                $aggregator->fill([
                    'id' => Str::random(),
                    'file_name' => $filePath,
                    'mime_type' => 'json',
                    'txn_date' => Carbon::now(),
                ]);

                $aggregator->save();

                Log::info("Fetching data every five minute " . Carbon::now());

            })->$frequency();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
