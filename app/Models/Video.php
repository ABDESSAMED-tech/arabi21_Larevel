<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

use Illuminate\Support\Str;
class Video extends Model
{
    protected $guarded = [];


    protected $dates = [
        'published_on',
        'created_at',
        'updated_at',

    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    public function getDurationAttribute($duration)
    {
        return ($duration < 60) ? substr( gmdate("i:s", $duration), 1) : ltrim( Str::after(gmdate("H:i:s", $duration), "00:"), "0");
    }

    public function getFormattedPublishedOnDate(){
        Carbon::setlocale(config('youtube-api.local'));
        return Carbon::parse($this->published_on)->diffForHumans();
    }

    public function getFormattedThumbnailAttribute()
    {
        if($this->type == "youtube" && Str::contains($this->thumbnail, 'https://')){
            return "https://img.youtube.com/vi/{$this->video_id}/mqdefault.jpg";
        }
        return Str::contains($this->thumbnail, 'https://') ? $this->thumbnail : asset("storage/{$this->thumbnail}");
    }

    public function getTitleAttribute($value){
        return html_entity_decode($value, ENT_QUOTES);
    }

}
