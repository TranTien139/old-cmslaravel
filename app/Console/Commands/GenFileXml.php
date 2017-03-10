<?php

namespace App\Console\Commands;

use App\Logicals\LogicalArticle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class GenFileXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'genfilexml';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        ExportIA();
    }
}
