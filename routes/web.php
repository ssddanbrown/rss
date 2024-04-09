<?php

use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostViewController::class, 'home']);
Route::get('/t/{tag}', [PostViewController::class, 'tag']);
Route::get('/f/{feed}', [PostViewController::class, 'feed']);

Route::get('/feed', [FeedController::class, 'get']);
