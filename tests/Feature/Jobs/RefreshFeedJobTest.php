<?php

namespace Tests\Feature\Jobs;

use App\Jobs\RefreshFeedJob;
use App\Models\Feed;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RefreshFeedJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_posts_for_the_given_feed()
    {
        $feed = Feed::factory()->create(['url' => 'https://example.com/feed.xml']);
        $job = new RefreshFeedJob($feed);
        Http::fake([
            'example.com/*' => Http::response(<<<END
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<rss version="2.0">
  <channel>
    <item>
      <title>BookStack Release v22.06</title>
      <link>https://www.bookstackapp.com/blog/bookstack-release-v22-06/</link>
      <pubDate>Fri, 24 Jun 2022 11:00:00 +0000</pubDate>

      <guid>https://www.bookstackapp.com/blog/bookstack-release-v22-06/</guid>
      <description>A little description</description>
    </item>
  </channel>
</rss>
END)
        ]);

        $this->assertEquals(0, $feed->posts()->count());

        dispatch_sync($job);

        /** @var Post[] $posts */
        $posts = $feed->posts()->get();
        $this->assertCount(1, $posts);

        $this->assertDatabaseHas('posts', [
            'feed_id' => $feed->id,
            'title' => 'BookStack Release v22.06',
            'url' => 'https://www.bookstackapp.com/blog/bookstack-release-v22-06/',
            'description' => 'A little description',
            'published_at' => 1656068400,
        ]);
    }

}
