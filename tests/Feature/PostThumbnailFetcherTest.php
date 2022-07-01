<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Rss\PostThumbnailFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PostThumbnailFetcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_fetch()
    {
        $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==');
        Http::fake([
            'example.com/*' => Http::response(<<<END
<html>
<head>
<meta property="og:image" content="https://exampleb.com/image.png">
</head>
</html>
END),
            'exampleb.com/*' => Http::response($imageData),
        ]);
        $post = Post::factory()->create(['url' => 'http://example.com/cats']);

        $fetcher = new PostThumbnailFetcher();
        $result = $fetcher->fetchAndStoreForPost($post);

        $this->assertTrue($result);
        $expectedPath = storage_path("app/public/thumbs/{$post->feed_id}/{$post->id}.png");
        $this->assertFileExists($expectedPath);

        $content = file_get_contents($expectedPath);
        $this->assertEquals($imageData, $content);
        $this->assertEquals($post->thumbnail, "thumbs/{$post->feed_id}/{$post->id}.png");

        unlink($expectedPath);
    }
}
