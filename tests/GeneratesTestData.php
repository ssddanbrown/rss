<?php

namespace Tests;

use App\Config\ConfiguredFeedProvider;
use App\Models\Feed;
use App\Models\Post;

trait GeneratesTestData
{
    protected function generateStableTestData()
    {
        $config = <<<END
http://example.com/a.xml Feed_A[#F00] #Tech #News
http://example.com/b.xml Feed_B #Animals #News
http://example.com/c.xml Feed_C #testing
END;

        $this->app->singleton(ConfiguredFeedProvider::class, function ($app) use ($config) {
            $provider = new ConfiguredFeedProvider();
            $provider->loadFromString($config);
            return $provider;
        });

        $feeds = [
            Feed::factory(['url' => 'http://example.com/a.xml'])->create(),
            Feed::factory(['url' => 'http://example.com/b.xml'])->create(),
            Feed::factory(['url' => 'http://example.com/c.xml'])->create(),
        ];

        foreach ($feeds as $feed) {
            Post::factory(49)->create(['feed_id' => $feed->id]);
            Post::factory()->create([
                'title' => "Special title for feed {$feed->url}",
                'description' => "Special desc for feed {$feed->url}",
                'feed_id' => $feed->id,
            ]);
        }

        return [
            'config' => $config,
            'feeds' => $feeds,
        ];
    }
}
