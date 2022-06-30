<?php

namespace App\Http\Controllers;

use App\Config\ConfiguredFeedProvider;
use App\Rss\PostProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostViewController extends Controller
{

    public function __construct(
        protected PostProvider $postProvider,
        protected ConfiguredFeedProvider $feedProvider,
    ) {}

    public function home(Request $request)
    {
        $page = max(intval($request->get('page')), 1);

        $feeds = $this->feedProvider->getAll();
        $feeds->reloadOutdatedFeeds();
        $posts = $this->postProvider->getLatest(
            $feeds,
            100,
            $page
        );

        return Inertia::render('Home', [
            'feeds' => $feeds,
            'posts' => $posts,
            'page' => $page,
        ]);
    }
}
