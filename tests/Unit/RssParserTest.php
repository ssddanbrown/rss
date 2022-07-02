<?php

namespace Tests\Unit;

use App\Rss\RssParser;
use Tests\TestCase;

class RssParserTest extends TestCase
{
    public function test_it_returns_an_array()
    {
        $parser = new RssParser();

        $this->assertIsArray($parser->rssDataToPosts(''));
    }


    public function test_it_parses_valid_posts()
    {
        $parser = new RssParser();

        $posts = $parser->rssDataToPosts(<<<END
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<rss version="2.0">
  <channel>
    <item>
      <title>BookStack Release v22.06</title>
      <link>https://www.bookstackapp.com/blog/bookstack-release-v22-06/</link>
      <pubDate>Fri, 24 Jun 2022 11:00:00 +0000</pubDate>

      <guid>https://www.bookstackapp.com/blog/bookstack-release-v22-06/</guid>
      <description>BookStack v22.06 is now here! This release was primarily refinement focused but it does include some great new features that may streamline your usage of the platform.</description>
    </item>

    <item>
      <title>BookStack Release v22.04</title>
      <link>https://www.bookstackapp.com/blog/bookstack-release-v22-04/</link>
      <pubDate>Fri, 29 Apr 2022 12:00:00 +0000</pubDate>

      <guid>https://www.bookstackapp.com/blog/bookstack-release-v22-04/</guid>
      <description>Today brings the release of BookStack v22.04! This includes the much-awaited feature of easier page editor switching, in addition to a bunch of other additions and improvements.</description>
    </item>
  </channel>
</rss>
END
        );

        $this->assertCount(2, $posts);
        $this->assertEquals('BookStack Release v22.06', $posts[0]->title);
        $this->assertEquals('https://www.bookstackapp.com/blog/bookstack-release-v22-06/', $posts[0]->guid);
        $this->assertEquals('https://www.bookstackapp.com/blog/bookstack-release-v22-06/', $posts[0]->url);
        $this->assertEquals(1656068400, $posts[0]->published_at);
        $this->assertEquals('BookStack v22.06 is now here! This release was primarily refinement focused but it does include some great new features that may streamline your usage of the platform.', $posts[0]->description);
    }

    public function test_it_parses_single_post()
    {
        $parser = new RssParser();

        $posts = $parser->rssDataToPosts(<<<END
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<rss version="2.0">
  <channel>
    <item>
      <title>BookStack Release v22.06</title>
      <link>https://www.bookstackapp.com/blog/bookstack-release-v22-06/</link>
      <pubDate>Fri, 24 Jun 2022 11:00:00 +0000</pubDate>

      <guid>https://www.bookstackapp.com/blog/bookstack-release-v22-06/</guid>
      <description>BookStack v22.06 is now here! This release was primarily refinement focused but it does include some great new features that may streamline your usage of the platform.</description>
    </item>
  </channel>
</rss>
END
        );

        $this->assertCount(1, $posts);
        $this->assertEquals('BookStack Release v22.06', $posts[0]->title);
        $this->assertEquals('https://www.bookstackapp.com/blog/bookstack-release-v22-06/', $posts[0]->guid);
        $this->assertEquals('https://www.bookstackapp.com/blog/bookstack-release-v22-06/', $posts[0]->url);
        $this->assertEquals(1656068400, $posts[0]->published_at);
        $this->assertEquals('BookStack v22.06 is now here! This release was primarily refinement focused but it does include some great new features that may streamline your usage of the platform.', $posts[0]->description);
    }

    public function test_it_parses_no_posts()
    {
        $parser = new RssParser();

        $posts = $parser->rssDataToPosts(<<<END
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<rss version="2.0">
  <channel>
  </channel>
</rss>
END
        );

        $this->assertCount(0, $posts);
    }

    public function test_invalid_posts_are_not_returned()
    {
        $parser = new RssParser();

        $posts = $parser->rssDataToPosts(<<<END
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<rss version="2.0">
  <channel>
    <item>
      <title>Bad Link</title>
      <link>cats.com</link>
      <pubDate>Fri, 24 Jun 2022 11:00:00 +0000</pubDate>
      <guid>https://www.bookstackapp.com/blog/bookstack-release-v22-06/</guid>
      <description>Post Desc</description>
    </item>
     <item>
      <title></title>
      <link>https://www.bookstackapp.com/bad-title/</link>
      <pubDate>Fri, 24 Jun 2022 11:00:00 +0000</pubDate>
      <guid>https://www.bookstackapp.com/bad-title/</guid>
      <description>Post Desc</description>
    </item>
    <item>
      <title>Bad Date</title>
      <link>https://www.bookstackapp.com/blog/bookstack-release-v22-03/</link>
      <pubDate>Friday</pubDate>
      <guid>https://www.bookstackapp.com/blog/bookstack-release-v22-03/</guid>
      <description>Post Desc</description>
    </item>
  </channel>
</rss>
END
        );

        $this->assertCount(0, $posts);
    }

    public function test_descriptions_in_html_are_parsed()
    {
        $parser = new RssParser();

        $posts = $parser->rssDataToPosts(<<<END
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<rss version="2.0">
  <channel>
    <item>
      <title>BookStack Release v22.06</title>
      <link>https://www.bookstackapp.com/blog/bookstack-release-v22-06/</link>
      <pubDate>Fri, 24 Jun 2022 11:00:00 +0000</pubDate>
      <guid>https://www.bookstackapp.com/blog/bookstack-release-v22-06/</guid>
      <description>&lt;span a=&quot;b&quot;&gt;Some really cool text&lt;/span&gt; &amp;amp; with &amp;pound; entities within</description>
    </item>
  </channel>
</rss>
END
        );

        $this->assertEquals('Some really cool text & with £ entities within', $posts[0]->description);
    }

    public function test_it_parses_valid_atom_feeds()
    {
        $parser = new RssParser();

        $posts = $parser->rssDataToPosts(<<<END
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title type="text" xml:lang="en">Example Atom Feed</title>

  <entry>
    <title>Example Post A</title>
    <link href="https://example.com/a"/>
    <updated>2022-06-09T17:00:00.000Z</updated>
    <id>https://example.com/a</id>
    <content type="html">
      &lt;p&gt;Example Post A&lt;/p&gt;
      &lt;p&gt;&lt;a href="https://example/a"&gt;Read the full article&lt;/a&gt;&lt;/p&gt;
    </content>
    <author>
        <name>Example Team</name>
    </author>
  </entry>

  <entry>
    <title>Example Post B</title>
    <link href="https://example.com/b"/>
    <updated>2022-06-08T17:00:00.000Z</updated>
    <id>https://example.com/b</id>
    <content type="html">
      &lt;p&gt;Example Post B&lt;/p&gt;
      &lt;p&gt;&lt;a href="https://example/a"&gt;Read the full article&lt;/a&gt;&lt;/p&gt;
    </content>
    <author>
        <name>Example Team</name>
    </author>
  </entry>
</feed>
END
        );

        $this->assertCount(2, $posts);
        $this->assertEquals('Example Post A', $posts[0]->title);
        $this->assertEquals('https://example.com/a', $posts[0]->guid);
        $this->assertEquals('https://example.com/a', $posts[0]->url);
        $this->assertEquals(1654794000, $posts[0]->published_at);
        $this->assertEquals("Example Post A Read the full article", $posts[0]->description);
    }

    public function test_atom_summary_used_over_content()
    {
        $parser = new RssParser();

        $posts = $parser->rssDataToPosts(<<<END
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title type="text" xml:lang="en">Example Atom Feed</title>

  <entry>
    <title>Example Post A</title>
    <link href="https://example.com/a"/>
    <updated>2022-06-09T17:00:00.000Z</updated>
    <id>https://example.com/a</id>
    <content type="html">&lt;p&gt;Example Post A Content&lt;/p&gt;</content>
    <summary type="html">&lt;p&gt;Example Post A Summary&lt;/p&gt;</summary>
    <author>
        <name>Example Team</name>
    </author>
  </entry>
</feed>
END
        );

        $this->assertEquals("Example Post A Summary", $posts[0]->description);
    }

    public function test_switcher_summary_used_over_content()
    {
        $parser = new RssParser();

        $posts = $parser->rssDataToPosts(<<<END
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title type="text" xml:lang="en">Example Atom Feed</title>

  <entry>
    <title>Example Post A</title>
    <link href="https://example.com/a"/>
    <updated>2022-06-09T17:00:00.000Z</updated>
    <id>https://example.com/a</id>
    <content type="html">&lt;p&gt;Example Post A Content&lt;/p&gt;</content>
    <summary type="html">&lt;p&gt;Example Post A Summary&lt;/p&gt;</summary>
    <author>
        <name>Example Team</name>
    </author>
  </entry>
</feed>
END
        );

        $this->assertEquals("Example Post A Summary", $posts[0]->description);
    }

}
