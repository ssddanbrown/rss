<?php

use App\Config\ConfiguredFeedProvider;
use App\Rss\PostProvider;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Home', [
        'name' => 'Barry Scott',
    ]);
});

Route::get('/feeds', function(PostProvider $postProvider, ConfiguredFeedProvider $configuredFeedProvider) {

    $feeds = $configuredFeedProvider->getAll();
    $feeds->reloadOutdatedFeeds();
    $posts = $postProvider->getLatest($feeds, 100);

    return response()->json([
        'feeds' => $feeds,
        'posts' => $posts,
    ]);
});
