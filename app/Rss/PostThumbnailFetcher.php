<?php

namespace App\Rss;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PostThumbnailFetcher
{

    public function fetchAndStoreForPost(Post $post): bool
    {
        $imageUrl = $this->getThumbLinkFromUrl($post->url);
        if (!$imageUrl) {
            return false;
        }

        $imageInfo = $this->downloadImageFromUrl($imageUrl);
        if (!$imageInfo) {
            return false;
        }

        $path = "thumbs/{$post->feed_id}/{$post->id}.{$imageInfo['extension']}";
        $complete = Storage::disk('public')->put($path, $imageInfo['data']);
        if (!$complete) {
            return false;
        }

        $post->thumbnail = $path;
        $post->save();

        return true;
    }

    /**
     * @return null|array{extension: string, data: string}
     */
    protected function downloadImageFromUrl(string $url): ?array
    {
        $imageResponse = Http::timeout(5)->get($url);
        if (!$imageResponse->successful()) {
            return null;
        }

        $imageData = $imageResponse->body();
        // > 1MB
        if (strlen($imageData) > 1000000) {
            return null;
        }

        $tempFile = tmpfile();
        fwrite($tempFile, $imageData);
        $mimeSplit = explode('/', mime_content_type($tempFile) ?: '');
        if (count($mimeSplit) < 2 || $mimeSplit[0] !== 'image') {
            return null;
        }

        $extension = $mimeSplit[1];
        return ['data' => $imageData, 'extension' => $extension];
    }

    protected function getThumbLinkFromUrl(string $url): string
    {
        $pageResponse = Http::timeout(5)->get($url);
        if (!$pageResponse->successful()) {
            return '';
        }

        $postHead = substr($pageResponse->body(), 0, 100000);
        $metaMatches = [];
        $metaPattern = '/<meta [^<>]*property=["\']og:image["\'].*?>/';
        if (!preg_match($metaPattern, $postHead, $metaMatches)) {
            return '';
        }

        $linkMatches = [];
        $linkPattern = '/content=["\'](.*?)["\']/';
        if (!preg_match($linkPattern, $metaMatches[0], $linkMatches)) {
            return '';
        }

        $link = $linkMatches[1];

        if (!str_starts_with($link, 'https://') && !str_starts_with($link, 'http://')) {
            return '';
        }

        return $link;
    }

}
