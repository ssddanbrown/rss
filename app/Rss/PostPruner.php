<?php

namespace App\Rss;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class PostPruner
{
    /**
     * Prune all posts older than the given number of days.
     * Returns the number of posts deleted.
     */
    public function prune(int $retentionDays): int
    {
        $day = 86400;
        $oldestAcceptable = time() - ($retentionDays * $day);
        $deleteCount = 0;

        Post::query()
            ->where('published_at', '<', $oldestAcceptable)
            ->select(['id', 'thumbnail'])
            ->chunk(250, function (Collection $posts) use (&$deleteCount) {
                $deleteCount += $posts->count();
                $this->deletePosts($posts);
            });

        return $deleteCount;
    }

    /**
     * @param Collection<Post> $posts
     */
    protected function deletePosts(Collection $posts)
    {
        $storage = Storage::disk('public');

        foreach ($posts as $post) {
            if ($post->thumbnail && $storage->exists($post->thumbnail)) {
                $storage->delete($post->thumbnail);
            }
        }

        Post::query()
            ->whereIn('id', $posts->pluck('id')->all())
            ->delete();
    }
}
