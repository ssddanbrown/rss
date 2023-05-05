<?php

namespace App\Console\Commands;

use App\Config\ConfiguredFeedProvider;
use Illuminate\Console\Command;

class UpdateFeedsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:update-outdated-feeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger the update of outdated feeds';

    /**
     * Execute the console command.
     */
    public function handle(ConfiguredFeedProvider $feedProvider): int
    {
        $feeds = $feedProvider->getAll();
        $feeds->reloadOutdatedFeeds();

        return 0;
    }
}
