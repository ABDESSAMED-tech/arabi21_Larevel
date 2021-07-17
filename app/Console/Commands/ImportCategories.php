<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade as Goutte;

class ImportCategories extends Command
{

    protected $signature = 'categories:import';

    protected $description = 'Import Arabi21 website categories';


    public function handle()
    {
        $crawler = Goutte::request('GET', config('crawler.rss.index'));
        $crawler->filter('.rssIcon a')->each(function ($node) {
            $category = [
                'id' => Str::after($node->attr('href'), 'id='),
                'name' => $node->text()
            ];
            if (intval($category['id']) < 0) {
                return;
            }

            Category::updateOrCreate(
                ['id' => $category["id"]],
                $category
            );
            $this->info("Category Imported : {$category['id']} \t {$category['name']}");
        });
    }
}
