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

    public function test_to_string()
    {
        $config = new RssConfig();
        $config->addFeedUrl('https://example.com', ['#cat', '#dog']);
        $config->addFeedUrl('https://example-B.com/cats?test=abc#okay');

        $expected = "https://example.com #cat #dog\nhttps://example-B.com/cats?test=abc#okay";
        $this->assertEquals($expected, $config->toString());
    }

    public function test_parse_from_string()
    {
        $config = new RssConfig();
        $config->parseFromString("
https://example-B.com/cats?test=abc#okay
https://example.com #dog #cat
# A comment

http://beans.com/feed.xml#food #cooking
        ");

        $this->assertCount(3, $config->getFeedUrls());
        $this->assertCount(0, $config->getTags('https://example-B.com/cats?test=abc#okay'));
        $this->assertEquals(['#dog', '#cat'], $config->getTags('https://example.com'));
        $this->assertEquals(['#cooking'], $config->getTags('http://beans.com/feed.xml#food'));
    }


    public function test_encode_for_url_without_compression()
    {
        $config = new RssConfig();
        $config->addFeedUrl('https://a.com', ['#a', '#b']);

        $expected = 'thttps%3A%2F%2Fa.com+%23a+%23b';
        $this->assertEquals($expected, $config->encodeForUrl());
    }

    public function test_encode_for_url_with_compression()
    {
        $config = new RssConfig();
        $config->addFeedUrl('https://a.com', ['#a', '#b']);
        $config->addFeedUrl('https://b.com', ['#a', '#b']);

        $expected = 'ceJzLKCkpKLbS10/US87PVVBOVFBO4sqAiiUhxAD4igvQ';
        $this->assertEquals($expected, $config->encodeForUrl());
    }

    public function test_decode_from_url_without_compression()
    {
        $config = new RssConfig();
        $config->decodeFromUrl('thttps%3A%2F%2Fa.com+%23a+%23b%0Ahttps%3A%2F%2Fb.com+%23a+%23b');

        $this->assertCount(2, $config->getFeedUrls());
        $this->assertEquals(['#a', '#b'], $config->getTags('https://a.com'));
        $this->assertEquals(['#a', '#b'], $config->getTags('https://b.com'));
    }

    public function test_decode_from_url_with_compression()
    {
        $config = new RssConfig();
        $config->decodeFromUrl('ceJzLKCkpKLbS10/US87PVVBOVFBO4sqAiiUhxAD4igvQ');

        $this->assertCount(2, $config->getFeedUrls());
        $this->assertEquals(['#a', '#b'], $config->getTags('https://a.com'));
        $this->assertEquals(['#a', '#b'], $config->getTags('https://b.com'));
    }

    public function test_decode_from_url_with_empty_input()
    {
        $config = new RssConfig();
        $config->decodeFromUrl('');

        $this->assertCount(0, $config->getFeedUrls());
    }
}
