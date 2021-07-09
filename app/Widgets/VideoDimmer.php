<?php

namespace App\Widgets;

use App\Models\Video;
use Illuminate\Support\Str;
use TCG\Voyager\Widgets\BaseDimmer;

class VideoDimmer extends BaseDimmer
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
        $count = Video::count();
        $string = 'videos';

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-youtube-play',
            'title'  => "{$count} {$string}",
            'text'   => __('voyager::dimmer.post_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => __('View all videos'),
                'link' => route('voyager.videos.index'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/02.jpg'),
        ]));
    }


}
