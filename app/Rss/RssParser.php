<?php

namespace App\Rss;

use App\Models\Post;
use DateTime;
use Exception;
use SimpleXMLElement;

class RssParser
{

    /**
     * @return Post[]
     */
    public function rssDataToPosts(string $rssData): array
    {
        try {
            return $this->parseRssDataToPosts($rssData);
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * @return Post[]
     * @throws Exception
     */
    public function parseRssDataToPosts(string $rssData): array
    {
        $rssXml = new SimpleXMLElement($rssData);
        $items = iterator_to_array($rssXml->channel->item, false);
        $posts = [];

        foreach ($items as $item) {

            $date = DateTime::createFromFormat('D, d M Y H:i:s T', $item->pubDate ?? '');
            $postData = [
                'title' => substr(strval($item->title ?? ''), 0, 250),
                'description' => substr(strval($item->description ?? ''), 0, 1000),
                'url' => strval($item->link ?? ''),
                'published_at' => $date ? $date->getTimestamp() : 0,
            ];

            if (!$this->isValidRssData($postData)) {
                continue;
            }

            $posts[] = (new Post())->forceFill($postData);
        }

        return $posts;
    }

    /**
     * @param array{title: string, description: string, url: string, published_at: int} $item
     */
    protected function isValidRssData(array $item): bool
    {
        if (empty($item['title']) || empty($item['url'])) {
            return false;
        }

        if (!str_starts_with($item['url'], 'https://') && !str_starts_with($item['url'], 'http://')) {
            return false;
        }

        if ($item['published_at'] <= 1000) {
            return false;
        }

        return true;
    }
}
