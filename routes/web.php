<?php

use App\Jobs\GetPostImage;
use App\Jobs\ImportPosts;
use App\Models\Category;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;
use Vedmant\FeedReader\Facades\FeedReader;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect("/api");
});
Route::get('/schedule', function () {
    Artisan::queue('youtube:fetch');
    Artisan::queue('youtube:process');
    return redirect("/api");
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
