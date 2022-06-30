<?php

namespace App\Config;

use App\Models\Feed;
use JsonSerializable;

class ConfiguredFeed implements JsonSerializable
{
    public bool $reloading = false;

    public function __construct(
        public Feed $feed,
        public string $name,
        public string $url,
        public array $tags
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'tags' => $this->tags,
            'reloading' => $this->reloading,
        ];
    }
}
