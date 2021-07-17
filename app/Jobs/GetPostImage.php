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


    public function __construct($postId)
    {
        $this->postId = $postId;
    }

    public function handle()
    {
        $post = Post::select(['id', 'img'])->find($this->postId);
        if(!$post || $post->img){
            return;
        }
        $data = OpenGraph::fetch(config("crawler.post_base_url") . $post->id);

        if(isset($data['image'])){
            $post->setAttribute('img', $data['image'])->save();
        }
    }
}
