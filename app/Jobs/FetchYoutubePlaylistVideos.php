<?php

namespace App\Jobs;

use App\Models\Video;
use Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class FetchYoutubePlaylistVideos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $nextPageToken;
    private $playlist;
    private $import;

    public function __construct($playlist, $nextPageToken = null, $import = false)
    {
        $this->playlist = $playlist;
        $this->nextPageToken = $nextPageToken;
        $this->import = $import;
    }


    public function handle()
    {
        $results = $this->fetchVideos();
        if(!$results){
            $this->playlist->setAttribute("active", false);
            $this->playlist->save();
            return;
        }
        $this->playlist->setAttribute("processed", 1)->save();
        $this->addVideos($results);
    }

    private function fetchVideos(){

        $params = [
            'type' => 'video',
            'playlistId' =>  $this->playlist->playlist_id,
            'part' => 'snippet',
            'maxResults' => 50,
            'key' => config('youtube-api.key1')
        ];

        if($this->nextPageToken){
            $params['pageToken'] = $this->nextPageToken;
        }
        $req  = Http::timeout(15)->get(config('youtube-api.yt_playlist_api'), $params);
        return $req->ok() ? $req->json() : false;
    }

    private function addVideos($results){
        print "{$this->playlist->title}: \t\t\t " . count($results["items"]) . " videos will be imported...\n";
        foreach ($results["items"] as $video) {
            if (
                (isset($video["snippet"]["liveBroadcastContent"]) &&  $video["snippet"]["liveBroadcastContent"] != "none")
                || Str::contains(strtolower($video["snippet"]["title"]), ['live','deleted video', 'private video'])
            )
                continue;

            $videoRec = [
                'video_id' => $video["snippet"]["resourceId"]["videoId"],
                'program_id' => $this->playlist->id,
                'title' => $video["snippet"]["title"],
                'description' => $video["snippet"]["description"],
                'thumbnail' => $this->getThumbnail($video["snippet"]["thumbnails"]),
                'published_on' => Carbon::createFromTimeString($video["snippet"]["publishedAt"]),
                'url' => "https://www.youtube.com/watch?v={$video['snippet']['resourceId']['videoId']}",
                "type" => 'youtube',
            ];
            Video::updateOrCreate(
                ['video_id' => $videoRec['video_id']],
                $videoRec
            );
        }

        if(isset($results["nextPageToken"]) && $this->import){
            print "Importing the next page ....\n\n";
            FetchYoutubePlaylistVideos::dispatch($this->playlist, $results["nextPageToken"], $this->import);
        }
    }

    private function getThumbnail($thumbnails){
        if(isset($thumbnails['maxres']))
            return $thumbnails['maxres']['url'];
        if(isset($thumbnails['high']))
            return $thumbnails['high']['url'];
        if(isset($thumbnails['medium']))
            return $thumbnails['medium']['url'];
        if(isset($thumbnails['default']))
            return $thumbnails['default']['url'];
        return null;
    }
}
