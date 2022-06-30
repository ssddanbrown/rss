<?php

namespace App\Rss;

use App\Config\ConfiguredFeedList;
use App\Models\Post;

class PostProvider
{
    public function getLatest(ConfiguredFeedList $feeds, int $count)
    {
        return Post::query()
            ->whereIn('feed_id', $feeds->getFeedIds())
            ->orderBy('published_at', 'desc')
            ->take($count)
            ->get();
    }
}
