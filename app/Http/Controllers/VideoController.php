<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Video as VideoResource;
use App\Models\Video;
use YouTube\YouTubeDownloader;


class VideoController extends Controller
{

    public function index()
    {
        $videos = Video::whereEnabled( true)
            ->whereActive(true)
            ->latest("published_on")
            ->paginate(50);
        return VideoResource::collection($videos);
    }

    public function show(Video $video)
    {
        $resource = new VideoResource($video);
        if ($video->type == "youtube") {
            $yt = new YouTubeDownloader();
            // $resource->additional(['data' => ['links' => $yt->getDownloadLinks($video->url)]]);
            $resource->additional(['data' => ['links' => []]]);
        }
        return $resource;
    }
}
