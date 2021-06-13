<?php

namespace App\Console\Commands;

use Alaouy\Youtube\Facades\Youtube;
use App\Jobs\FetchYoutubePlaylists as JobsFetchYoutubePlaylists;
use App\Models\Program;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FetchYoutubePlaylists extends Command
{

    protected $signature = 'youtube:playlists';


    protected $description = 'Fetch Youtube channel playlists';


    public function handle()
    {
       JobsFetchYoutubePlaylists::dispatch();
    }

}
