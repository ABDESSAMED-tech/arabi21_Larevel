<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Post as PostResource;
use App\Http\Resources\PostCollection;
use App\Models\Post;
use Illuminate\Support\Facades\View;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{

    public function index()
    {
        $posts = QueryBuilder::for(Post::class)
            ->allowedFilters(['title', 'content', 'author'])
            ->orderBy("id", "DESC")
            ->cursorPaginate(15);
        return new PostCollection($posts->items());
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
