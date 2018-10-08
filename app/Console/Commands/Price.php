<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MyClass\timeDeal;

class Price extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'change price everyday';

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
        $timeDeal = new timeDeal();
        $timeDeal->allDeal();
    }
}
