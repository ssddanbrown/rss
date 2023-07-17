<?php

namespace App\Http\Controllers;

use App\Config\ConfiguredFeedList;
use App\Config\ConfiguredFeedProvider;
use App\Rss\PostProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostViewController extends Controller
{
    public function __construct(
        protected PostProvider $postProvider,
        protected ConfiguredFeedProvider $feedProvider,
    ) {
    }

    public function home(Request $request)
    {
        $displayFeeds = $this->feedProvider->getAll();
        $displayFeeds->reloadOutdatedFeeds();

        $postFeeds = $this->feedProvider->getVisible();

        return $this->renderPostsView($request, $displayFeeds, $postFeeds);
    }

    public function tag(Request $request, string $tag)
    {
        $feeds = $this->feedProvider->getForTag('#' . $tag);
        $feeds->reloadOutdatedFeeds();

        return $this->renderPostsView($request, $feeds, $feeds, ['tag' => $tag]);
    }

    public function feed(Request $request, string $feed)
    {
        $feed = urldecode($feed);

        $feeds = $this->feedProvider->getAsList($feed);
        $feeds->reloadOutdatedFeeds();

        return $this->renderPostsView($request, $feeds, $feeds, ['feed' => $feed]);
    }

    protected function renderPostsView(Request $request, ConfiguredFeedList $displayFeeds, ConfiguredFeedList $postFeeds, array $additionalData = [])
    {
        $page = max(intval($request->get('page')), 1);
        $query = $request->get('query', '');
        $subFilter = null;

        if ($query) {
            $subFilter = function (Builder $where) use ($query) {
                $where->where('title', 'like', '%' . $query . '%')
                    ->orWhere('description', 'like', '%' . $query . '%');
            };
        }

        $posts = $this->postProvider->getLatest(
            $postFeeds,
            100,
            $page,
            $subFilter
        );

        $coreData = [
            'feeds' => $displayFeeds,
            'posts' => $posts,
            'page' => $page,
            'search' => $query,
            'tag' => '',
            'feed' => '',
        ];

        return Inertia::render('Posts', array_merge($coreData, $additionalData));
    }
}
