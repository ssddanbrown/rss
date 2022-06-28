<?php

namespace Tests\Unit;

use App\Config\RssConfig;
use PHPUnit\Framework\TestCase;

class RssConfigTest extends TestCase
{

    public function test_get_feed_urls()
    {
        $config = new RssConfig();
        $config->addFeedUrl('https://example.com');
        $config->addFeedUrl('https://example-b.com/cats?test=abc#okay');

        $urls = $config->getFeedUrls();
        $this->assertCount(2, $urls);
        $this->assertEquals('https://example.com', $urls[0]);
        $this->assertEquals('https://example-b.com/cats?test=abc#okay', $urls[1]);
    }

    public function test_remove_feed_url()
    {
        $config = new RssConfig();
        $config->addFeedUrl('https://example.com');
        $config->addFeedUrl('https://example-B.com/cats?test=abc#okay');

        $existingRemoved = $config->removeFeedUrl('https://example-B.com/cats?test=abc#okay');
        $nonExistingRemoved = $config->removeFeedUrl('https://example-c.com');

        $this->assertTrue($existingRemoved);
        $this->assertFalse($nonExistingRemoved);
        $this->assertCount(1, $config->getFeedUrls());
    }

}
