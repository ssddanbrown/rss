<?php

namespace App\Config;

use App\Jobs\RefreshFeedJob;
use App\Models\Feed;
use JsonSerializable;

class ConfiguredFeed implements JsonSerializable
{
    public bool $reloading = false;

    public function __construct(
        public Feed $feed,
        public string $name,
        public string $url,
        public string $color,
        public array $tags,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'name' => $this->name,
            'color' => $this->color,
            'url' => $this->url,
            'tags' => $this->tags,
            'reloading' => $this->reloading,
            'outdated' => $this->isOutdated(),
        ];
    }

    public function isOutdated(): bool
    {
        $configFrequency = intval(config('app.feed_update_frequency'));
        $expiry = time() - intval($configFrequency * 60);
        return $this->feed->last_fetched_at <= $expiry;
    }

    public function startReloading(): void
    {
        $refreshJob = new RefreshFeedJob($this->feed);
        dispatch($refreshJob);
        $this->reloading = true;
    }
}
