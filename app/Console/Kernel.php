<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        //
    ];


    protected function schedule(Schedule $schedule)
    {
        $schedule->call('App\Http\Controllers\Services\ServicesController@collector')
                ->everyFiveMinutes();
                //->withoutOverlapping();
        
        // $schedule->command('inspire')
        //          ->hourly();
        
        //$schedule->call(function () {
           // DB::table('recent_users')->delete();
        //})->everyFiveMinutes()->withoutOverlapping();
    }


    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
