<?php

namespace App\Config;

use App\Models\Feed;

class ConfiguredFeedProvider
{
    protected RssConfig $config;

    /** @var ConfiguredFeed[]  */
    protected $feeds = [];

    public function loadFromConfig(): void
    {
        $this->config = new RssConfig();

        $configFilePath = config('app.config_file');
        if ($configFilePath && file_exists($configFilePath)) {
            $contents = file_get_contents($configFilePath);
            $this->config->parseFromString($contents);
            $this->feeds = $this->getConfiguredFeeds();
        }
    }

    public function loadFromString(string $config): void
    {
        $this->config = new RssConfig();
        $this->config->parseFromString($config);
        $this->feeds = $this->getConfiguredFeeds();
    }

    /**
     * @return ConfiguredFeed[]
     */
    protected function getConfiguredFeeds(): array
    {
        $configuredFeeds = [];
        $feedUrls = $this->config->getFeedUrls();
        $feeds = Feed::query()->whereIn('url', $feedUrls)->get()->keyBy('url');

        foreach ($feedUrls as $feedUrl) {
            $feed = $feeds->get($feedUrl);
            if (!$feed) {
                $feed = (new Feed())->forceCreate([
                    'url' => $feedUrl,
                    'last_fetched_at' => 0,
                    'last_accessed_at' => 0,
                ]);
            }

            $configured = new ConfiguredFeed(
                $feed,
                $this->config->getName($feedUrl),
                $feedUrl,
                $this->config->getColor($feedUrl),
                $this->config->getTags($feedUrl)
            );

            $configuredFeeds[] = $configured;
        }

        return $configuredFeeds;
    }

    protected function updateLastAccessedForFeeds(array $feeds)
    {
        $ids = array_map(fn (ConfiguredFeed $feed) => $feed->feed->id, $feeds);

        Feed::query()->whereIn('id', $ids)->update([
            'last_accessed_at' => now()
        ]);
    }

    public function getAll()
    {
        $this->updateLastAccessedForFeeds($this->feeds);
        return new ConfiguredFeedList($this->feeds);
    }

    public function get(string $feedUrl): ?ConfiguredFeed
    {
        foreach ($this->feeds as $feed) {
            if ($feed->url === $feedUrl) {
                $this->updateLastAccessedForFeeds([$feed]);
                return $feed;
            }
        }

        return null;
    }

    public function getAsList(string $feedUrl): ConfiguredFeedList
    {
        $feed = $this->get($feedUrl);
        $feeds = $feed ? [$feed] : [];
        return new ConfiguredFeedList($feeds);
    }

    public function getForTag(string $tag)
    {
        $feeds = array_filter($this->feeds, function (ConfiguredFeed $feed) use ($tag) {
            return in_array($tag, $feed->tags);
        });

        $this->updateLastAccessedForFeeds($feeds);
        return new ConfiguredFeedList($feeds);
    }
}
