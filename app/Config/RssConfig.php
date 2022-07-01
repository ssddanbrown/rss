<?php

namespace App\Config;

class RssConfig
{

    /**
     * The configured feeds.
     * Array keys are the feed URLs and values are arrays of tags as strings.
     * Tag strings include their '#' prefix.
     * @var array<string, array{name: string, tags: string[], color: string}>
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
    public function addFeed(string $feed, string $name, array $tags = [], string $color = ''): void
    {
        $this->feeds[$feed] = [
            'name' => $name,
            'tags' => $tags,
            'color' => $color,
        ];
    }

    /**
     * Remove a feed from the config.
     * Returns a boolean indicating if the feed existed.
     */
    public function removeFeed(string $feed): bool
    {
        $exists = isset($this->feeds[$feed]);

        if ($exists) {
            unset($this->feeds[$feed]);
        }

        return $exists;
    }

    /**
     * Get the tags for the given feed.
     * @return string[]
     */
    public function getTags(string $feed): array
    {
        return $this->feeds[$feed]['tags'] ?? [];
    }

    /**
     * Get the name for the given feed.
     */
    public function getName(string $feed): string
    {
        return $this->feeds[$feed]['name'] ?? '';
    }

    /**
     * Get the color for the given feed.
     */
    public function getColor(string $feed): string
    {
        return $this->feeds[$feed]['color'] ?? '';
    }

    /**
     * Get the configuration as a string.
     */
    public function toString(): string
    {
        $lines = [];

        foreach ($this->feeds as $feed => $details) {
            $line = "{$feed} {$details['name']}";
            if ($details['color']) {
                $line .= "[{$details['color']}]";
            }

            $tags = $details['tags'];

            foreach ($tags as $tag) {
                $line .= " {$tag}";
            }

            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    /**
     * Parse out RSS feeds from the given string.
     */
    public function parseFromString(string $configString): void
    {
        $lines = explode("\n", $configString);

        foreach ($lines as $line) {
            $line = trim($line);
            $parts = explode(' ', $line);

            if (empty($line) || str_starts_with($line, '#') || count($parts) < 2) {
                continue;
            }

            $url = $parts[0];
            $name = $parts[1];
            $color = '';

            $matches = [];
            if (preg_match('/^(.*)\[(.*)\]$/', $name, $matches)) {
                $name = $matches[1];
                $color = $matches[2];
            }

            $name = str_replace('_', ' ', $name);
            $tags = array_filter(array_slice($parts, 2), fn($str) => str_starts_with($str, '#'));

            if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                $this->addFeed($url, $name, $tags, $color);
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

        $this->parseFromString($configStr);
    }

}
