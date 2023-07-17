<?php

namespace Tests\Feature;

use App\Config\ConfiguredFeed;
use App\Jobs\RefreshFeedJob;
use App\Models\Feed;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class ConfiguredFeedTest extends TestCase
{
    public function test_is_outdated_can_be_controlled_by_config(): void
    {
        $now = time();
        $feed = new Feed();
        $feed->last_fetched_at = $now - (59 * 60);

        $configuredFeed = new ConfiguredFeed(
            $feed,
            'My great feed',
            'https://example.com',
            '#fff',
            ['#a'],
            false,
        );

        config()->set('app.feed_update_frequency', 60);
        $this->assertFalse($configuredFeed->isOutdated());

        $feed->last_fetched_at = $now - (61 * 60);
        $this->assertTrue($configuredFeed->isOutdated());

        config()->set('app.feed_update_frequency', 5);
        $feed->last_fetched_at = $now - (4 * 60);
        $this->assertFalse($configuredFeed->isOutdated());

        $feed->last_fetched_at = $now - (6 * 60);
        $this->assertTrue($configuredFeed->isOutdated());
    }

    public function test_start_reloading_dispatched_refresh_job(): void
    {
        $configuredFeed = new ConfiguredFeed(
            new Feed(),
            'My great feed',
            'https://example.com',
            '#fff',
            ['#a'],
            false,
        );
        Queue::fake();

        $configuredFeed->startReloading();
        Queue::assertPushed(RefreshFeedJob::class);
    }
}
