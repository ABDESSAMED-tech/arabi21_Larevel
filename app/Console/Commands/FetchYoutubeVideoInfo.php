<?php

namespace App\Console\Commands;

use Alaouy\Youtube\Facades\Youtube;
use App\Models\Video;
use Carbon\CarbonInterval;
use Illuminate\Console\Command;
use FireworkWeb\SMPTE\Timecode;

class FetchYoutubeVideoInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:process {--limit=50 : Number of videos to process each time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates Youtube videos metadata';


    public function handle()
    {
        $total =  Video::whereType('youtube')
        ->whereEnabled(false)->count();
        $part = ['statistics', 'contentDetails'];
        Youtube::setApiKey(config('youtube-api.key2'));
        $videos = Youtube::getVideoInfo(
            Video::where('type', 'youtube')
                ->orderBy("enabled", 'ASC')
                ->orderBy("published_on", 'DESC')
                ->orderBy("updated_at", 'ASC')
                ->pluck("video_id")->take(intval($this->option('limit')))->toArray(),
            $part
        );
        $this->info("Begin processing ... " . count($videos) . " of ". $total);
        foreach ($videos as $video) {
            Video::updateOrCreate(
                ['video_id' => $video->id],
                [
                    'duration' => $this->getDuration($video->contentDetails->duration),
                    'views' => isset($video->statistics->viewCount) ? $video->statistics->viewCount : 0,
                    'likes' => isset($video->statistics->likeCount) ? $video->statistics->likeCount : 0,
                    'comments' => isset($video->statistics->commentCount) ? $video->statistics->commentCount : 0,
                    'enabled' => true,
                    'active' => true,
                ]
            );
            $this->info($video->id . "\t Processed");
        }
    }

    private function getDuration($durationStr)
    {
        try {
            $ci = CarbonInterval::fromString($durationStr);
            $tc = new Timecode($ci->format("%H:%I:%S:00"));
            return $tc->durationInSeconds();
        } catch (\Exception $e) {
            $this->error($durationStr);
            return 0;
        }
    }
}
