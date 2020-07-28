<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        //
        Commands\CollectorCron::class,
        Commands\ImportCron::class,
        Commands\BillingCron::class,
    ];


    protected function schedule(Schedule $schedule)
    {
        //$schedule->call('collector:cron')
        $schedule->command('collector:cron')
                    //->everyFiveMinutes()
                    //->everyTenMinutes();
                    //->everyThirtyMinutes();    
                    //->hourly();
                    ->withoutOverlapping();
                    
        //$schedule->call('import:cron')
        $schedule->command('import:cron')
                    //->everyFiveMinutes()
                    //->everyTenMinutes();
                    //->everyThirtyMinutes();    
                    //->hourly();
                    ->withoutOverlapping();
                    
        //$schedule->call('billing:cron')
        $schedule->command('billing:cron')
                    //->everyFiveMinutes()
                    //->everyTenMinutes();  
                    //->everyThirtyMinutes();    
                    //->hourly();
                    ->withoutOverlapping();
        
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
