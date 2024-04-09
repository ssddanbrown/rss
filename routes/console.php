<?php

use App\Console\Commands\PrunePostsCommand;
use App\Console\Commands\UpdateFeedsCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(UpdateFeedsCommand::class)->everyFiveMinutes();
Schedule::command(PrunePostsCommand::class, ['-n'])->daily();
