<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Vedmant\FeedReader\Facades\FeedReader;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReadRssFeed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private  $category_id;
    public function __construct($category_id)
    {
        $this->category_id = $category_id;
    }

    public function handle()
    {
        $f = FeedReader::read(config('crawler.rss.url') . $this->category_id);
        if($f->error){
            return;
        }
        foreach($f->get_items() as $item){
            Log::info("Importing single post: " . $item->get_title());
            dump("Importing single post: " . $item->get_title());
            ImportSinglePost::dispatch($item, $this->category_id);
        }
    }
}
