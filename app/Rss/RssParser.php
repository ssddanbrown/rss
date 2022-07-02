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
                'description' => $this->formatDescription(strval($item->description) ?: ''),
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

    protected function formatDescription(string $description): string
    {
        $decoded = html_entity_decode(strip_tags($description));
        
        if (strlen($decoded) > 200) {
            return substr($decoded, 0, 200) . '...';
        }

        return $decoded;
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
