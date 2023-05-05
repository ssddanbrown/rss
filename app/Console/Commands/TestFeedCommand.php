<?php

namespace App\Console\Commands;

use App\Models\Feed;
use App\Rss\FeedPostFetcher;
use Illuminate\Console\Command;

class TestFeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:test-feed {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the provided feed by running a non-queued test import';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(FeedPostFetcher $postFetcher): int
    {
        $url = $this->argument('url');

        $posts = $postFetcher->fetchForFeed((new Feed())->forceFill(['url' => $url]));
        if (count($posts) === 0) {
            $this->error('No posts fetched. Either data could not be fetched or the feed data was not recognised as valid.');
            return Command::FAILURE;
        }

        $count = count($posts);
        $this->line("Found {$count} posts:");
        foreach ($posts as $post) {
            $this->line("[{$post->url}] {$post->title}");
        }

        return Command::SUCCESS;
    }
}
