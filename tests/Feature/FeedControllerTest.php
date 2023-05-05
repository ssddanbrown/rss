<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\GeneratesTestData;
use Tests\TestCase;

final class FeedControllerTest extends TestCase
{
    use RefreshDatabase;
    use GeneratesTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generateStableTestData();
    }

    public function test_get_feed(): void
    {
        $resp = $this->get('/feed?url=' . urlencode('http://example.com/a.xml'));
        $resp->assertOk();
        $resp->assertJson([
            'name' => 'Feed A',
            'color' => '#F00',
            'tags' => ['#Tech', '#News'],
            'url' => 'http://example.com/a.xml'
        ]);
    }

    public function test_non_existing_feed(): void
    {
        $resp = $this->get('/feed?url=' . urlencode('http://example.com/abc.xml'));
        $resp->assertNotFound();
    }
}
