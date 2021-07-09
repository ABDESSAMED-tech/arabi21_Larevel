<?php


namespace App\Widgets;

use App\Models\Post;

use Illuminate\Support\Str;
use TCG\Voyager\Widgets\BaseDimmer;
class PostDimmer extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = Post::count();
        $string = __('Posts');

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-news',
            'title'  => "{$count} {$string}",
            'text'   => __('voyager::dimmer.post_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => __('View all posts'),
                'link' => route('voyager.posts.index'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/02.jpg'),
        ]));
    }
}
