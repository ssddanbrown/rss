<?php

namespace App\Config;

use App\Jobs\RefreshFeedJob;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

class ConfiguredFeedList implements IteratorAggregate, JsonSerializable
{
    /**
     * @var ConfiguredFeed[]
     */
    protected array $feeds;

    public function __construct(array $feeds = [])
    {
        $this->feeds = $feeds;
    }

    public function getFeedIds(): array
    {
        return array_map(fn (ConfiguredFeed $feed) => $feed->feed->id, $this->feeds);
    }

    public function reloadOutdatedFeeds(): int
    {
        $expiry = time() - 3600;
        $refreshCount = 0;

        foreach ($this->feeds as $feed) {
            if ($feed->feed->lasted_fetched_at <= $expiry) {
                $refreshJob = new RefreshFeedJob($feed->feed);
                dispatch($refreshJob);
                $refreshCount++;
            }
        }

        return $refreshCount;
    }

    public function getIterator(): Traversable
    {
        return $this->feeds;
    }

    public function jsonSerialize(): mixed
    {
        return $this->feeds;
    }
}
