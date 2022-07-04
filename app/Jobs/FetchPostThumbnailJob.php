<?php

namespace App\Jobs;

use App\Models\Post;
use App\Rss\PostThumbnailFetcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchPostThumbnailJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * How long this unique lock exists for this kind of job.
     */
    public int $uniqueFor = 7200;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Post $post
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PostThumbnailFetcher $thumbnailFetcher)
    {
        $thumbnailFetcher->fetchAndStoreForPost($this->post);
    }

    /**
     * Get the unique key for these jobs.
     */
    public function uniqueId(): string
    {
        return strval($this->post->id);
    }
}
