<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import tickets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        app()->call('App\Http\Controllers\Services\ServicesController@import');
    }
}
