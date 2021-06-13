<?php

namespace App\Console\Commands;

use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Instagram\Api;
use Instagram\Model\Media;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class FetchInstagramFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "instagram:fetch";
    //                            {account_id : Instagram account ID}
    //                            {--first=50 : Number of posts to load}
    //                            {--after= : Next page token}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Instagram public feed for a given account';

    public function handle()
    {
        $cachePool = new FilesystemAdapter('Instagram', 0, Storage::path('cache'));
        $api = new Api($cachePool);
        $api->login(env('IG_USER'), env('IG_PW'));
        $profile = $api->getProfile(env('IG_PROFILE'));

        $posts = $profile->getMedias();
        do {
            $profile = $api->getMoreMedias($profile);
            $posts = array_merge($posts, $profile->getMedias());
            sleep(1);
        } while ($profile->hasMoreMedias());

        dd($posts);
    }

    private function importFeed($feed)
    {
        $this->info("Feed fetch, start importing posts ...");
        foreach ($feed['data']['user']['edge_owner_to_timeline_media']["edges"] as $edge) {
            if (!$edge['node']['is_video']) {
                $this->warn("Not a video post {$edge['node']['shortcode']}, skipping!");
                continue;
            }

            $title = $edge['node']['edge_media_to_caption']['edges'][0]['node']['text'];
            $videoRec = [
                'video_id' => $edge['node']['shortcode'],
                'title' => $this->getTitle($title),
                'description' => $title,
                'thumbnail' => $edge['node']['thumbnail_src'],
                'published_on' => Carbon::createFromTimestamp($edge['node']['taken_at_timestamp']),
                'likes' => $edge['node']['edge_liked_by']['count'],
                'comments' => $edge['node']['edge_media_to_comment']['count'],
                'views' => $edge['node']['video_view_count'],
                "type" => 'instagram',
            ];

            Video::updateOrCreate(
                ['video_id' => $videoRec['video_id']],
                $videoRec
            );
            $this->info("Imported {$videoRec['video_id']}");
        }
        if ($feed['data']['user']['edge_owner_to_timeline_media']['page_info']['has_next_page']) {
            $next = $feed['data']['user']['edge_owner_to_timeline_media']['page_info']['end_cursor'];
            $this->info("Command: instagram:fetch {$this->argument('account_id')} --first {$this->option('first')} --after={$next}");
            Artisan::queue("instagram:fetch {$this->argument('account_id')} --first {$this->option('first')} --after={$next}");
        }
    }

    private function getFeed()
    {
        $url = 'https://www.instagram.com/graphql/query/';
        $params = [
            'query_id' => '17880160963012870',
            'id' => $this->argument("account_id"),
            'first' => $this->option('first'),
            'after' => $this->option('after')
        ];

        $this->info("Fetching : {$url}?" . http_build_query($params));
        return Http::timeout(15)->get(
            $url,
            $params
        );
    }
    private function getTitle($caption)
    {
        //        foreach(explode("\n", $caption) as $line){
        //            if(Str::contains($caption, "#")){}
        //        }

        return Str::before($caption, "\n");
    }
}
