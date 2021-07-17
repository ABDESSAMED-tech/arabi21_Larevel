<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FetchYoutubeVideos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $nextPageToken;

    public function __construct($nextPageToken = null)
    {
        $this->nextPageToken = $nextPageToken;
    }


    public function handle()
    {
        $results = $this->fetchVideos();
        if($results){
            $this->addVideos($results);
        }
    }

    private function fetchVideos(){
        $params = [
            'type' => 'video',
            'channelId' => config('youtube-api.channel_id'),
            'part' => 'id,snippet',
            'order' => 'date',
            'maxResults' => 50,
            'key' => config('youtube-api.key1')
        ];

        if($this->nextPageToken){
            $params['pageToken'] = $this->nextPageToken;
        }
        $req  = Http::timeout(15)->get(config('youtube-api.yt_search_api'), $params);
        return $req->ok() ? $req->json() : false;
    }

    private function addVideos($results){
        foreach ($results["items"] as $video) {
            // dd($video);
            if ($video["snippet"]["liveBroadcastContent"] != "none" || Str::contains(strtolower($video["snippet"]["title"]), 'live')) continue;
            $videoRec = [
                'video_id' => $video["id"]["videoId"],
                'title' => $video["snippet"]["title"],
                'description' => $video["snippet"]["description"],
                'thumbnail' => isset($video["snippet"]["thumbnails"]["high"]) ? $video["snippet"]["thumbnails"]["high"]["url"] : $video["snippet"]["thumbnails"]["medium"]["url"],
                'published_on' => Carbon::createFromTimeString($video["snippet"]["publishedAt"]),
                'url' => "https://www.youtube.com/watch?v={$video['id']['videoId']}",
                "type" => 'youtube',
            ];
            Video::updateOrCreate(
                ['video_id' => $videoRec['video_id']],
                $videoRec
            );

        }
        if(isset($results["nextPageToken"])){
            FetchYoutubeVideos::dispatch($results["nextPageToken"]);
        }
    }
}
