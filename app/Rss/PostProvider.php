<?php

namespace App\Rss;

use App\Config\ConfiguredFeedList;
use App\Models\Post;
use Illuminate\Support\Collection;

class PostProvider
{
    public function getLatest(ConfiguredFeedList $feeds, int $count, int $page, callable $condition = null)
    {
        $posts = Post::query()
            ->when($condition, $condition)
            ->whereIn('feed_id', $feeds->getFeedIds())
            ->orderBy('published_at', 'desc')
            ->take($count)
            ->skip(($page - 1) * $count)
            ->get();

        $this->loadFeedsToPostCollection($posts, $feeds);

        return $posts;
    }

    /**
     * @param Collection<Post> $posts
     */
    protected function loadFeedsToPostCollection(Collection $posts, ConfiguredFeedList $feeds): void
    {
        $feedsById = $feeds->getMappedById();

        foreach ($posts as $post) {
            $post->setAttribute('feed', $feedsById[$post->feed_id] ?? []);
        }
    }
}
