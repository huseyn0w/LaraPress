<?php

namespace App\Console\Commands;

use App\Services\CPanel\CPanelPostService;
use Illuminate\Console\Command;

/**
 * Publishes posts whose `scheduled_at` is due. Scheduled to run every minute
 * (App\Console\Kernel::schedule). Pure boundary: delegates to the service.
 */
class PublishDuePosts extends Command
{
    protected $signature = 'posts:publish-due';

    protected $description = 'Publish posts whose scheduled_at has arrived.';

    public function handle(CPanelPostService $service): int
    {
        $count = $service->publishDue();

        $this->info("Published {$count} due post(s).");

        return self::SUCCESS;
    }
}
