<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenFileHome extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'genfilehome';

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
        GenFileHome(68,'cliphot',0);
        GenFileHome(31,'hainuocngoai',1);
        GenFileHome(1,'haivietnam',1);
    }
}
