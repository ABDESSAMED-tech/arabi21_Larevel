<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class ImportPosts implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $items;
    private $category;

    public function __construct($items, $category = null)
    {
        $this->items = $items;
        $this->category = $category;
    }

    public function handle()
    {
        $records = [];
        $batchJobs = [];
        foreach($this->items as $item){
            $id = $this->getPostId($item->get_permalink());
            $records[] = [
                "id" => $id,
                "title" => $item->get_title(),
                "content" => $item->get_content(),
                "author" => $item->get_author(),
                "published_on" => $item->get_date('Y-m-d H:i:s'),
                "category_id" => $this->category,
                "fetched" => true
            ];
            $batchJobs[] = new GetPostImage($id);
            break;
        }

        DB::table('posts')->upsert($records, ["id", "title"]);
        Bus::batch($batchJobs)->dispatch();
    }

    private function getPostId($url){
        return explode("/", str_replace('//','/', parse_url($url, PHP_URL_PATH)))[2];
    }
}
