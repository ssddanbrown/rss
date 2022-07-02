<?php

namespace App\Rss;

use App\Models\Feed;
use App\Models\Post;
use Illuminate\Support\Facades\Http;

class FeedPostFetcher
{

    public function __construct(
        protected RssParser $rssParser
    ) {}

    /**
     * @return Post[]
     */
    public function fetchForFeed(Feed $feed): array
    {
        $feedResponse = Http::timeout(5)->get($feed->url);
        if (!$feedResponse->successful()) {
            return [];
        }

        $rssData = ltrim($feedResponse->body());
        $tagStart = explode(' ', substr($rssData, 0, 20))[0];
        $validStarts = ['<?xml', '<feed', '<rss'];
        if (!in_array($tagStart, $validStarts)) {
            return [];
        }

        return $this->rssParser->rssDataToPosts($rssData);
    }

}
