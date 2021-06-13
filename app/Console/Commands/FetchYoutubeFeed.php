<?php

namespace App\Console\Commands;

use Alaouy\Youtube\Facades\Youtube;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FetchYoutubeFeed extends Command
{
    protected $signature = 'youtube:fetch {--limit=500 : Number of videos to load}
                                          {--next= : Next page token}';

    protected $description = 'Get latest videos of a Youtube channel';


    public function handle()
    {
        $this->getLatestVideos();
    }

    private function getLatestVideos()
    {
        $params = [
            'type' => 'video',
            'channelId' => config('youtube-api.channel_id'),
            'part' => 'id, snippet',
            'maxResults' => $this->option('limit'),
            'order' => 'date'
        ];
        if ($this->option("next")) {
            $params["pageToken"] = $this->option("next");
        }

        $this->info("Fetching {$this->option('limit')} videos ...");
        Youtube::setApiKey(config('youtube-api.key1'));
        $videos = Youtube::searchAdvanced($params, true);
        $this->info(count($videos["results"]) . " videos loaded, importing now");
        foreach ($videos['results'] as $video) {
            if ($video->snippet->liveBroadcastContent != "none") continue;
            $videoRec = [
                'video_id' => $video->id->videoId,
                'title' => $video->snippet->title,
                'description' => $video->snippet->description,
                'thumbnail' => isset($video->snippet->thumbnails->high) ? $video->snippet->thumbnails->high->url : $video->snippet->thumbnails->medium->url,
                'published_on' => Carbon::createFromTimeString($video->snippet->publishedAt),
                'url' => "https://www.youtube.com/watch?v={$video->id->videoId}",
                "type" => 'youtube',
            ];
            Video::updateOrCreate(
                ['video_id' => $videoRec['video_id']],
                $videoRec
            );
            $this->info($videoRec['video_id'] . " -> Imported");
        }
                if ($videos['info']["nextPageToken"]) {
                    $this->info("php artisan youtube:fetch  --next={$videos['info']["nextPageToken"]}");
                  //  Artisan::queue("youtube:fetch --next={$videos['info']["nextPageToken"]}");
                }
    }
}
