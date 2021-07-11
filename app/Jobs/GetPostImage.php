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
use shweshi\OpenGraph\Facades\OpenGraphFacade as OpenGraph;

class GetPostImage implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $postId;

    const BASE_URL = "https://arabi21.com/story/";

    public function __construct($postId)
    {
        $this->postId = $postId;
    }

    public function handle()
    {
        $data = OpenGraph::fetch(self::BASE_URL . $this->postId);
        if(isset($data['image'])){
            Post::whereId($this->postId)
            ->update(['img'=>$data['image']]);
        }
    }
}
