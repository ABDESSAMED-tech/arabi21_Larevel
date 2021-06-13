<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Alaouy\Youtube\Facades\Youtube;
use Illuminate\Support\Str;
use App\Models\Program;

class FetchYoutubePlaylists implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $nextPageToken;


    public function __construct($nextPageToken = null)
    {
        $this->nextPageToken = $nextPageToken;
    }

    public function handle()
    {
        $playlistIds = [];
        $params = ["maxResults" => 50];
        if($this->nextPageToken){
            $params['pageToken'] = $this->nextPageToken;
        }
        Youtube::setApiKey(config('youtube-api.key3'));
        $playlists = Youtube::getPlaylistsByChannelId(config('youtube-api.channel_id'),  $params);

        print ("\n" . count($playlists["results"]) . " lists loaded, importing now...\n\n");
        foreach ($playlists['results'] as  $playlist) {
            $programRec = [
                'playlist_id' => $playlist->id,
                'title' => $playlist->snippet->title,
                'description' => $playlist->snippet->description,
                'thumbnail' => $this->getThumbnail($playlist),
            ];
            Program::updateOrCreate(
                ['playlist_id' => $programRec['playlist_id']],
                $programRec
            );
            $playlistIds[] = $playlist->id;
            print "Playlist {$playlist->id} \timported\n";
        }
        if(isset($playlists["info"]["nextPageToken"]) && $playlists["info"]["nextPageToken"] != false){
            FetchYoutubePlaylists::dispatch($playlists["info"]["nextPageToken"]);
        }
    }

    private function getThumbnail($playlist){
        if($program = Program::where('playlist_id', $playlist->id)->first()){
            if(!empty($program->thumbnail) && !Str::contains($program->thumbnail, "https://")) {
                return $program->thumbnail;
            }
        }
        return $playlist->snippet->thumbnails->medium->url;
    }
}
