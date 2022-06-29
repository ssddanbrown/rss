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

}
