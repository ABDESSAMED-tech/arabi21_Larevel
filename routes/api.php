<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/register', 'UsersController@register');
Route::post('/login', 'UsersController@login');
Route::get('/logout', 'UsersController@logout')->middleware('auth:api');

Route::get('/', function () {
    return [
        "version" => config("settings.app.version")
    ];
});

Route::fallback(function () {
    return response()->json([
        "error" => [
            'status' => 404,
            'title'  => 'Invalid endpoint',
            'detail' => 'Resource not found'
        ]
    ]);
});
Route::apiResource('posts', 'PostController');
Route::apiResource('categories', 'CategoryController');
Route::apiResource('videos', 'VideoController')->only(['index', 'show']);
Route::apiResource('programs', 'ProgramController')->only(['index', 'show']);
