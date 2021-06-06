<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Post as PostResource;
use App\Http\Resources\PostCollection;
use App\Models\Post;
use Illuminate\Support\Facades\View;
use Spatie\QueryBuilder\QueryBuilder;

class PostController extends Controller
{

    public function index()
    {
        $posts = QueryBuilder::for(Post::class)
            ->allowedFilters(['title', 'content', 'author'])
            ->latest("published_on")
            ->paginate()
            ->appends(request()->query());
        return new PostCollection($posts);
    }
    public function create()
    {
        return [];
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return new PostResource($post);
    }
}
