<?php

namespace Tests\Feature\Commands;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Storage;
use Tests\GeneratesTestData;
use Tests\TestCase;

class PrunePostsCommandTest extends TestCase
{
    use RefreshDatabase;
    use GeneratesTestData;

    public function test_command_deletes_posts_older_than_days_given()
    {
        $now = time();
        $day = 86400;

        Post::factory(11)->create(['published_at' => $now - ($day * 2)]);
        Post::factory(13)->create(['published_at' => $now - ($day * 0.5)]);

        $this->assertEquals(24, Post::query()->count());

        $this->artisan('rss:prune-posts --days=1')
            ->expectsConfirmation('This will delete all posts older than 1 day(s). Do you want to continue?', 'yes')
            ->expectsOutput('Deleted 11 posts from the system')
            ->assertExitCode(0);

        $this->assertEquals(13, Post::query()->count());
    }

    public function test_command_deletes_post_thumbnail_if_existing()
    {
        $post = Post::factory()->createOne(['published_at' => 50]);
        $thumb = 'thumbs/' . Str::random() . '.png';
        $post->thumbnail = $thumb;
        $post->save();

        Storage::disk('public')->put($thumb, 'test-img-data');

        $this->assertTrue(Storage::disk('public')->exists($thumb));

        $this->artisan('rss:prune-posts --days=1')
            ->expectsConfirmation('This will delete all posts older than 1 day(s). Do you want to continue?', 'yes')
            ->assertExitCode(0);

        $this->assertFalse(Storage::disk('public')->exists($thumb));
    }

    public function test_command_defaults_to_config_option_time()
    {
        Post::factory()->createOne(['published_at' => time() - (86400 * 10.1)]);
        Post::factory()->createOne(['published_at' => time() - (86400 * 9.5)]);
        config()->set('app.prune_posts_after_days', 10);

        $this->assertEquals(2, Post::query()->count());

        $this->artisan('rss:prune-posts')
            ->expectsConfirmation('This will delete all posts older than 10 day(s). Do you want to continue?', 'yes')
            ->assertExitCode(0);

        $this->assertEquals(1, Post::query()->count());
    }

    public function test_command_defaults_to_no_action_if_config_false()
    {
        Post::factory()->createOne(['published_at' => time() - (86400 * 10.1)]);
        config()->set('app.prune_posts_after_days', false);

        $this->assertEquals(1, Post::query()->count());

        $this->artisan('rss:prune-posts')
            ->expectsOutput('No prune retention time set therefore no posts will be pruned.')
            ->assertExitCode(0);

        $this->assertEquals(1, Post::query()->count());
    }

    public function test_command_deletes_all_posts_in_range()
    {
        Post::factory(500)->create(['published_at' => time() - (86400 * 10.1)]);

        $this->assertEquals(500, Post::query()->count());

        $this->artisan('rss:prune-posts --days=3')
            ->expectsConfirmation('This will delete all posts older than 3 day(s). Do you want to continue?', 'yes')
            ->assertExitCode(0);

        $this->assertEquals(0, Post::query()->count());
    }
}
