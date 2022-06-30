<?php

namespace App\Config;

use App\Models\Feed;

class ConfiguredFeedProvider
{
    protected RssConfig $config;

    /** @var ConfiguredFeed[]  */
    protected $feeds = [];

    public function loadFromEnvironment(): void
    {
        $this->config = new RssConfig();

        $config = session()->get('rss_config', null);
        if ($config) {
            $this->config->parseFromString($config);
            $this->feeds = $this->getConfiguredFeeds();
            return;
        }

        $configFilePath = config('app.config_file');
        if ($configFilePath && file_exists($configFilePath)) {
            $contents = file_get_contents($configFilePath);
            $this->config->parseFromString($contents);
            $this->feeds = $this->getConfiguredFeeds();
            return;
        }
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
                    'lasted_fetched_at' => 0,
                ]);
            }

            $configured = new ConfiguredFeed(
                $feed,
                $this->config->getName($feedUrl),
                $feedUrl,
                $this->config->getTags($feedUrl)
            );

            $configuredFeeds[] = $configured;
        }

        return $configuredFeeds;
    }

    public function getAll()
    {
        return new ConfiguredFeedList($this->feeds);
    }

    public function getForTag(string $tag)
    {
        $feeds = array_filter($this->feeds, function (ConfiguredFeed $feed) use ($tag) {
            return in_array($tag, $feed->tags);
        });

        return new ConfiguredFeedList($feeds);
    }
}
