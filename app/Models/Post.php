<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImgAttribute($value)
    {
        if (Str::contains($value, "http")) {
            return $value;
        }
        return 'http://www.2er.net/Content/Upload/large/' . $value;
    }
}
