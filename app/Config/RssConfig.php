<?php

namespace App\Config;

class RssConfig
{

    /**
     * The configured feeds.
     * Array keys are the feed URLs and values are arrays of tags as strings.
     * Tag strings include their '#' prefix.
     * @var array<string, string[]>
     */
    protected $feeds = [];

    /**
     * Get all feed URLs
     * @returns string[]
     */
    public function getFeedUrls(): array
    {
        return array_keys($this->feeds);
    }

    /**
     * Add a new feed to the config.
     */
    public function addFeedUrl(string $feed, array $tags = []): void
    {
        $this->feeds[$feed] = $tags;
    }

    /**
     * Remove a feed from the config.
     * Returns a boolean indicating if the feed existed.
     */
    public function removeFeedUrl(string $feed): bool
    {
        $exists = isset($this->feeds[$feed]);

        if ($exists) {
            unset($this->feeds[$feed]);
        }

        return $exists;
    }

    /**
     * Get the configuration as a string.
     */
    public function toString(): string
    {
        $lines = [];

        foreach ($this->feeds as $feed => $tags) {
            $line = $feed;
            foreach ($tags as $tag) {
                $line .= " {$tag}";
            }
            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    /**
     * Decode a config from the given string.
     */
    public function decodeFromString(string $configString): void
    {
        $lines = explode("\n", $configString);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '#') {
                continue;
            }

            $parts = explode(' ', $line);
            $url = $parts[0];
            $tags = array_filter(array_slice($parts, 1), fn($str) => str_starts_with($str, '#'));

            if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                $this->addFeedUrl($url, $tags);
            }
        }
    }

    /**
     * Encode to be used for a URL.
     * Attempts two different formats, url encoded vs compressed, and
     * returns the smaller.
     * URL encoded format is prefixed with a 't'.
     * Compressed format is prefixed with a 'c'.
     */
    public function encodeForUrl(): string
    {
        $configString = $this->toString();

        $urlEncoded = 't' . urlencode($configString);
        $compressed = 'c' . base64_encode(gzcompress($configString));

        return strlen($urlEncoded) > strlen($compressed) ? $compressed : $urlEncoded;
    }

    /**
     * Decode the config from the given URL encoded config string that's
     * been created using the `encodeForUrl()` function.
     */
    public function decodeFromUrl(string $urlConfigString): void
    {
        if (empty($urlConfigString)) {
            return;
        }

        $typeByte = $urlConfigString[0];
        $configStr = '';

        if ($typeByte === 't') {
            $configStr = urldecode(substr($urlConfigString, 1));
        }

        if ($typeByte === 'c') {
            $configStr = gzuncompress(base64_decode(substr($urlConfigString, 1)));
        }

        $this->decodeFromString($configStr);
    }

}
