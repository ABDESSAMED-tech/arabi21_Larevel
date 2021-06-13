<?php

namespace App\Http\Resources;

use App\Http\Resources\v1\Video as VideoResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Program extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "thumbnail" => $this->formatted_thumbnail,
            "videos" => VideoResource::collection($this->videos)
        ];
    }
}
