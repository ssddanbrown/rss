<?php

namespace Tests\Unit;

use App\Config\RssConfig;
use PHPUnit\Framework\TestCase;

class RssConfigTest extends TestCase
{
    public function test_get_feed_urls()
    {
        $config = new RssConfig();
        $config->addFeed('https://example.com', 'example a');
        $config->addFeed('https://example-b.com/cats?test=abc#okay', 'example b');

        $urls = $config->getFeedUrls();
        $this->assertCount(2, $urls);
        $this->assertEquals('https://example.com', $urls[0]);
        $this->assertEquals('https://example-b.com/cats?test=abc#okay', $urls[1]);
    }

    public function test_remove_feed_url()
    {
        $config = new RssConfig();
        $config->addFeed('https://example.com', 'a');
        $config->addFeed('https://example-B.com/cats?test=abc#okay', 'b');

        $existingRemoved = $config->removeFeed('https://example-B.com/cats?test=abc#okay');
        $nonExistingRemoved = $config->removeFeed('https://example-c.com');

        $this->assertTrue($existingRemoved);
        $this->assertFalse($nonExistingRemoved);
        $this->assertCount(1, $config->getFeedUrls());
    }

    public function test_to_string()
    {
        $config = new RssConfig();
        $config->addFeed('https://example.com', 'a', ['#cat', '#dog']);
        $config->addFeed('https://example-B.com/cats?test=abc#okay', 'b');

        $expected = "https://example.com a #cat #dog\nhttps://example-B.com/cats?test=abc#okay b";
        $this->assertEquals($expected, $config->toString());
    }

    public function test_parse_from_string()
    {
        $config = new RssConfig();
        $config->parseFromString("
https://example-B.com/cats?test=abc#okay a
https://example.com b[#000] #dog #cat
# A comment
https://example-C.com/cats?test=abc#okay

http://beans.com/feed.xml#food d_is_cool #cooking
        ");

        $this->assertCount(3, $config->getFeedUrls());
        $this->assertCount(0, $config->getTags('https://example-B.com/cats?test=abc#okay'));
        $this->assertEquals(['#dog', '#cat'], $config->getTags('https://example.com'));
        $this->assertEquals(['#cooking'], $config->getTags('http://beans.com/feed.xml#food'));
        $this->assertEquals('a', $config->getName('https://example-B.com/cats?test=abc#okay'));
        $this->assertEquals('b', $config->getName('https://example.com'));
        $this->assertEquals('#000', $config->getColor('https://example.com'));
        $this->assertEquals('d is cool', $config->getName('http://beans.com/feed.xml#food'));
    }


    public function test_encode_for_url_without_compression()
    {
        $config = new RssConfig();
        $config->addFeed('https://a.com', 'a', ['#a', '#b']);

        $expected = 'thttps%3A%2F%2Fa.com+a+%23a+%23b';
        $this->assertEquals($expected, $config->encodeForUrl());
    }

    public function test_encode_for_url_with_compression()
    {
        $config = new RssConfig();
        $config->addFeed('https://a.com', 'a', ['#a', '#b']);
        $config->addFeed('https://b.com', 'b', ['#a', '#b']);

        $expected = 'ceJzLKCkpKLbS10/US87PVUhUUAaiJK4MqGgSWDQJIgoAKUYM0w==';
        $this->assertEquals($expected, $config->encodeForUrl());
    }

    public function test_decode_from_url_without_compression()
    {
        $config = new RssConfig();
        $config->decodeFromUrl('thttps%3A%2F%2Fa.com+a+%23a+%23b%0Ahttps%3A%2F%2Fb.com+b+%23a+%23b');

        $this->assertCount(2, $config->getFeedUrls());
        $this->assertEquals(['#a', '#b'], $config->getTags('https://a.com'));
        $this->assertEquals(['#a', '#b'], $config->getTags('https://b.com'));
        $this->assertEquals('a', $config->getName('https://a.com'));
        $this->assertEquals('b', $config->getName('https://b.com'));
    }

    public function test_decode_from_url_with_compression()
    {
        $config = new RssConfig();
        $config->decodeFromUrl('ceJzLKCkpKLbS10/US87PVUhUUAaiJK4MqGgSWDQJIgoAKUYM0w==');

        $this->assertCount(2, $config->getFeedUrls());
        $this->assertEquals(['#a', '#b'], $config->getTags('https://a.com'));
        $this->assertEquals(['#a', '#b'], $config->getTags('https://b.com'));
        $this->assertEquals('a', $config->getName('https://a.com'));
        $this->assertEquals('b', $config->getName('https://b.com'));
    }

    public function test_decode_from_url_with_empty_input()
    {
        $config = new RssConfig();
        $config->decodeFromUrl('');

        $this->assertCount(0, $config->getFeedUrls());
    }
}
