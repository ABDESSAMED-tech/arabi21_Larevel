<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Log;

class ImportSinglePost implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $item;
    private $category;

    public function __construct($item, $category = null)
    {
        $this->item = $item;
        $this->category = $category;
    }

    public function handle()
    {

        $id = $this->getPostId($this->item->get_permalink());
        Post::updateOrCreate(
            ["id" => $id],
            [
                "id" => $id,
                "title" => $this->item->get_title(),
                "content" => $this->item->get_content(),
                "author" => $this->item->get_author(),
                "published_on" => $this->item->get_date('Y-m-d H:i:s'),
                "category_id" => $this->category,
                "fetched" => true
            ]
        );
        Log::info("Fetching post image: " . $id);
        dump("Fetching post image: " . $id);
        GetPostImage::dispatch($id);
    }

    private function getPostId($url)
    {
        return explode("/", str_replace('//', '/', parse_url($url, PHP_URL_PATH)))[2];
    }
}
