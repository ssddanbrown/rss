<?php

namespace App\Console\Commands;

use App\Jobs\RefreshFeedJob;
use App\Models\Feed;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UpdateFeedsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:update-outdated-feeds
        {--all : Also update dormant feeds}
        {--outdated-time=24 : Age (in hours) that\'s considered as outdated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger the processing of outdated feeds';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $queryDormant = $this->hasOption('all');
        $dormantTime = time() - (86400 * 90); // 90 days
        $outdatedTime = time() - ceil(floatval($this->option('outdated-time')) * 3600);
        $dormantQuery = function(Builder $query) use ($dormantTime) {
            $query->where('last_accessed_at', '>', $dormantTime);
        };

        Feed::query()
            ->when(!$queryDormant, $dormantQuery)
            ->where('last_fetched_at', '<', $outdatedTime)
            ->chunk(100, function(Collection $feeds) {
                /** @var Feed $feed */
                foreach ($feeds as $feed) {
                    dispatch(new RefreshFeedJob($feed));
                }
            });

        return 0;
    }
}
