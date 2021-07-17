<?php

namespace App\Console\Commands;

use App\Jobs\ImportPosts as JobsImportPosts;
use App\Models\Category;
use Illuminate\Console\Command;
use Vedmant\FeedReader\Facades\FeedReader;

class ImportPosts extends Command
{

    protected $signature = 'posts:import';

    protected $description = 'Import Posts';


    public function handle()
    {
        $categories = Category::select(['id', 'name'])
        ->get();
        foreach($categories as $category){
            $f = FeedReader::read(config('crawler.rss.url') . $category->id);
            if($f->error){
                continue;
            }
            $this->info("Importing:\t" . count($f->get_items()) . "\tposts from: " . config('crawler.rss.url') . $category->id);
            JobsImportPosts::dispatch($f->get_items(), $category->id);
        }
    }
}
