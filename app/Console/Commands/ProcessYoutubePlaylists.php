<?php

namespace App\Console\Commands;

use Alaouy\Youtube\Facades\Youtube;
use App\Jobs\FetchYoutubePlaylistVideos;
use App\Models\Program;;

use Illuminate\Console\Command;

class ProcessYoutubePlaylists extends Command
{

    protected $signature = 'youtube:process-playlists {playlist_id?} {--import}';


    protected $description = 'Fetch and assign videos by playlist';

    public function handle()
    {
        Youtube::setApiKey(config('youtube-api.key1'));
        $playlists = Program::whereNotNull("playlist_id")
            ->when($this->argument("playlist_id"), function($q){
                $q->where("playlist_id", $this->argument("playlist_id"));
            })
            ->orderBy("processed", "ASC")->get();
        foreach ($playlists as $playlist) {
            FetchYoutubePlaylistVideos::dispatch($playlist, null, $this->option('import'));
        }
    }
}
