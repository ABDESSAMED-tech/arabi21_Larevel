<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Post as PostResource;
use App\Http\Resources\PostCollection;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class PostController extends Controller
{

    public function index()
    {
        $posts = QueryBuilder::for(Post::class)
            ->allowedFilters(['title', 'content', 'author', AllowedFilter::exact('category_id')])
            ->orderBy("id", "DESC")
            ->cursorPaginate(50);
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
    public function favoritePost(Request $request)
    {
        $success = false;
        if($request->filled('user_id') && $request->filled('post_id')){
            $user = User::findOrFail(request()->user_id);
            $post = Post::findOrFail(request()->post_id);
            $user->favorites()->syncWithoutDetaching($post->id);
            $success = true;
        }
        return [
            "success" => $success,
        ];
    }
    public function unfavoritePost(Request $request)
    {
        $success = false;
        if($request->filled('user_id') && $request->filled('post_id')){
            $user = User::findOrFail(request()->user_id);
            $post = Post::findOrFail(request()->post_id);
            $user->favorites()->detach($post->id);
            $success = true;
        }
        return [
            "success" => $success,
        ];
    }

    public function getFavoritePosts(User $user)
    {
       return $user->favorites()->get();
    }


}
