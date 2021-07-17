<?php

namespace App\Console\Commands;

use App\Jobs\ReadRssFeed;
use App\Models\Category;
use Illuminate\Console\Command;


class ImportPosts extends Command
{

    protected $signature = 'posts:import';

    protected $description = 'Import Posts';


    public function handle()
    {
        $categories = Category::select(['id', 'name'])
        ->orderBy('id', 'desc')
        ->get();
        foreach($categories as $category){
            $this->info("Importing posts from: " . config('crawler.rss.url') . $category->id);
            ReadRssFeed::dispatch($category->id);
        }
    }
}
