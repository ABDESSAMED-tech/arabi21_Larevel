<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Post extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'excerpt'=>$this->excerpt,
            'content'=>$this->content,
            'author'=>$this->author,
            'image'=>$this->img,
            'category'=> $this->category,
            'published_on'=>$this->published_on,
            'favourite' => $this->fav,
        ];
    }
}
