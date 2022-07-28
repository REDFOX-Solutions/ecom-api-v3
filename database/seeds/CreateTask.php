<?php

use App\Model\Task;
use Illuminate\Database\Seeder;

class CreateTask extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tasks = [
//            [
//                'title' => 'It will run every minute',
//                'type' => 'everyMinute',
//                'frequency' => 'everyMinute'
//            ],
            [
                'title' => 'It will run every five minute',
                'type' => 'everyFiveMinutes',
                'frequency' => 'everyFiveMinutes'
            ],
//            [
//                'title' => 'It will run daily',
//                'type' => 'daily',
//                'frequency' => 'daily'
//            ],
//            [
//                'title' => 'It will run every month',
//                'type' => 'monthly',
//                'frequency' => 'monthly'
//            ]
        ];

        foreach ($tasks as $key => $task) {
            Task::create($task);
        }
    }
}
