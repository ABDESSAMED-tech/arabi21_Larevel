<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Video extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            "id" => $this->id,
            "video_id" => $this->video_id,
            "title" => $this->title,
            "description" => $this->description,
            "type" => $this->type,
            "published_on" => $this->getFormattedPublishedOnDate(),
            "thumbnail" => $this->formatted_thumbnail,
            "url" => $this->url,
            "likes" => $this->likes,
            "comments" => $this->comments,
            "views" => $this->views,
            "duration" => $this->duration
        ];
    }
}
