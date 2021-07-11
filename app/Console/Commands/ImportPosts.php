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
        $categories = Category::select('id')->get();
        foreach($categories as $category){
            $f = FeedReader::read(config('youtube-api.rss') . $category->id);
            if($f->error){
                continue;
            }
            JobsImportPosts::dispatch($f->get_items(), $category->id);
            break;
        }
    }
}
