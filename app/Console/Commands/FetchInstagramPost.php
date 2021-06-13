<?php

namespace App\Console\Commands;

use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchInstagramPost extends Command
{

    protected $signature = 'instagram:process {--limit=50 : Number of videos to process each time}';

    protected $description = 'Fetch Instagram post details';
    const AGENT_DEFAULT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36';
    public function handle()
    {
        $videos = Video::where('type', 'instagram')
            ->orderBy("active", 'ASC')
            ->orderBy("published_on", 'DESC')
            ->orderBy("updated_at", 'ASC')
            ->take($this->option('limit'))->get();

        foreach ($videos as $video) {
            $this->updateMedia($video);
            sleep(1);
            break;
        }
    }

    private function getPostMedia($shortcode)
    {
        $this->info("https://www.instagram.com/p/{$shortcode}/?__a=1");

        //        $headers = [
        //            'headers' => [
        //                'user-agent' => UserAgentHelper::AGENT_DEFAULT,
        //                'x-requested-with' => 'XMLHttpRequest',
        //            ],
        //            'cookies' => $this->session->getCookies()
        //        ];
        $http = Http::timeout(15)->withHeaders([
            'user-agent' => $this::AGENT_DEFAULT,
            'x-requested-with' => 'XMLHttpRequest',
        ])->get("https://www.instagram.com/p/{$shortcode}/?__a=1");
        //
        if ($http->successful() && $http->json()) {
            return $http->json()['graphql']['shortcode_media'];
        } else {
            dump($http->body());
        }
        return false;
    }

    private function updateMedia($video)
    {
        $node = $this->getPostMedia($video->video_id);
        if (!$node) {
            return null;
        }
        //        dump($node);
        $this->info("Updating {$video->video_id}");
        $video->views = $node['video_view_count'];
        $video->url = $node['video_url'];
        $video->duration = intval($node['video_duration']);
        $video->thumbnail = $node['display_url'];
        $video->published_on = Carbon::createFromTimestamp($node['taken_at_timestamp']);
        $video->active = true;

        if (!empty($node['title'])) {
            $this->warn("Updating title: {$node['title']}");
            $video->title = $node['title'];
        }
        return $video->save();
    }
}
