<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Program extends Model
{
    protected $guarded = [];
    public function videos()
    {
        return $this->hasMany(Video::class)
            ->whereActive(1)
            ->whereEnabled(1)
            ->latest("published_on")
            ->take(50);
    }

    public function getThumbnailBrowseAttribute()
    {
        return $this->thumbnail ? $this->thumbnail : setting('admin.admin_no_thumbnail');
    }

    public function getFormattedThumbnailAttribute()
    {
        return Str::contains($this->thumbnail, 'https://') ? $this->thumbnail : asset("storage/{$this->thumbnail}");
    }
}
