<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProcessAllVideoCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all video commands';

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
        Artisan::queue("instagram:fetch 13297266445");
        Artisan::queue("instagram:process");
        Artisan::queue("youtube:fetch UCrj-RCA9B-Jg8RMEyMdp1KA");
        Artisan::queue("youtube:playlists UCrj-RCA9B-Jg8RMEyMdp1KA");
        Artisan::queue("youtube:process");
        Artisan::queue("youtube:process-playlists");
    }
}
