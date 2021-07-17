<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $items;
    private $category;

    public function __construct($items, $category = null)
    {
        $this->items = $items;
        $this->category = $category;
    }

    public function handle()
    {
        foreach($this->items as $item){
            $id = $this->getPostId($item->get_permalink());
            Post::updateOrCreate(["id" => $id],
            [
                "id" => $id,
                "title" => $item->get_title(),
                "content" => $item->get_content(),
                "author" => $item->get_author(),
                "published_on" => $item->get_date('Y-m-d H:i:s'),
                "category_id" => $this->category,
                "fetched" => true
            ]);
            GetPostImage::dispatch($id);
        }
    }

    private function getPostId($url){
        return explode("/", str_replace('//','/', parse_url($url, PHP_URL_PATH)))[2];
    }
}
