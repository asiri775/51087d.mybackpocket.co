<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class ResetUncatCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uncategorize-default-category:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Un-categorize Default Category';

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
     * @return int
     */
    public function handle()
    {
        DB::table('transactions')
        ->where('category_id','90')
        ->update([
            'category_id' => '125',
        ]);
    }
}
