<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
        return config("settings.app.uploads") . $value;
    }

    public function users(){
        return $this->belongsToMany(User::class, 'favorites', 'user_id', 'post_id')->withTimeStamps();
    }

    public function getFavAttribute()
    {
        if(!Auth::check()){
            return false;
        }

        return $this->belongsToMany(User::class, 'favorites', 'user_id', 'post_id')
        ->withTimestamps()
        ->where('user_id', Auth::user()->id)
        ->first();
    }
}
