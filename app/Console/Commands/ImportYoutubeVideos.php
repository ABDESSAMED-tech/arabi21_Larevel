<?php

namespace App\Console\Commands;

use App\Jobs\FetchYoutubeVideos;
use Illuminate\Console\Command;

class ImportYoutubeVideos extends Command
{
    protected $signature = 'youtube:import';

    protected $description = 'Import all Youtube channel videos';

    public function handle()
    {
        FetchYoutubeVideos::dispatch();
        return 0;
    }
}
