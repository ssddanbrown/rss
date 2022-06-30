<?php

namespace App\Rss;

use App\Config\ConfiguredFeedList;
use App\Models\Post;
use Illuminate\Support\Collection;

class PostProvider
{
    public function getLatest(ConfiguredFeedList $feeds, int $count, int $page)
    {
        $posts = Post::query()
            ->whereIn('feed_id', $feeds->getFeedIds())
            ->orderBy('published_at', 'desc')
            ->take($count)
            ->skip(($page - 1) * $count)
            ->get();

        $this->loadTagsToPostCollection($posts, $feeds);

        return $posts;
    }

    /**
     * @param Collection<Post> $posts
     */
    protected function loadTagsToPostCollection(Collection $posts, ConfiguredFeedList $feeds): void
    {
        $tagsByFeedId = $feeds->getTagMap();

        foreach ($posts as $post) {
            $post->setAttribute('tags', $tagsByFeedId[$post->feed_id] ?? []);
        }
    }
}
