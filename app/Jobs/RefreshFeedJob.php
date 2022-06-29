<?php

namespace App\Jobs;

use App\Models\Feed;
use App\Rss\FeedPostFetcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshFeedJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        protected Feed $feed
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FeedPostFetcher $postFetcher)
    {
        $freshPosts = $postFetcher->fetchForFeed($this->feed);

        foreach ($freshPosts as $post) {
            $this->feed->posts()->updateOrCreate(
                ['url' => $post->url],
                $post->getAttributes(),
            );
        }
    }

    /**
     * Get the unique key for these jobs.
     */
    public function uniqueId(): string
    {
        return strval($this->feed->id);
    }
}