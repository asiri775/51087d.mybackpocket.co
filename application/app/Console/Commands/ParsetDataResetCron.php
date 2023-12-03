<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class ParsetDataResetCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Parset database products , purchases and transactions ';

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
        DB::table('products')->truncate();
        DB::table('transactions')->truncate();
        DB::table('purchases')->truncate();
    }
}
