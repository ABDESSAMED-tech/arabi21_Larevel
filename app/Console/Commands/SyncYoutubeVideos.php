<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncYoutubeVideos extends Command
{

    protected $signature = 'youtube:sync {--limit=50 : Number of videos to sync}';

    protected $description = 'Sync youtube videos';

    public function handle()
    {
        $videos = Video::where("type", "youtube")
            ->latest()
            ->limit($this->option('limit'))
            ->get();
        $this->info('Processing: ' .  $this->option('limit') . ' videos');
        foreach ($videos as $video) {
            $this->videoExists($video);
        }
    }

    private function videoExists($video)
    {
        $response = Http::timeout(15)->get("https://noembed.com/embed?url=http://www.youtube.com/watch?v={$video->video_id}");

        dump($response->ok());
        dd($response->json(true));
        $this->info($video->video_id);
        $headers = get_headers("http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v={$video->video_id}");

        if (!strpos($headers[0], '200')) {
            $this->warn("Deleting: {$video->video_id}");
            $video->delete();
        } else {
            $this->info("Video exists: {$video->video_id}");
        }
    }
}
