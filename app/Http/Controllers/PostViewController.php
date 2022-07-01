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

        return Inertia::render('Posts', [
            'feeds' => $feeds,
            'posts' => $posts,
            'page' => $page,
        ]);
    }

    public function tag(Request $request, string $tag)
    {
        $page = max(intval($request->get('page')), 1);

        $feeds = $this->feedProvider->getForTag('#' . $tag);
        $feeds->reloadOutdatedFeeds();
        $posts = $this->postProvider->getLatest(
            $feeds,
            100,
            $page
        );

        return Inertia::render('Posts', [
            'feeds' => $feeds,
            'posts' => $posts,
            'page' => $page,
            'tag' => $tag,
        ]);
    }

    public function feed(Request $request, string $feed)
    {
        $page = max(intval($request->get('page')), 1);
        $feed = urldecode($feed);

        $feeds = $this->feedProvider->getAsList($feed);
        $feeds->reloadOutdatedFeeds();
        $posts = $this->postProvider->getLatest(
            $feeds,
            100,
            $page
        );

        return Inertia::render('Posts', [
            'feeds' => $feeds,
            'posts' => $posts,
            'page' => $page,
            'feed' => $feed,
        ]);
    }
}
