<?php

namespace App\Console\Commands;

use App\Catalog\Jobs\SyncCatalogJob;
use Illuminate\Console\Command;

class SyncCatalogCommand extends Command
{
    protected $signature = 'shopify:catalog:sync {--sync : Run inline instead of queueing}';

    protected $description = 'Reconcile the local Shopify catalogue mirror';

    public function handle(): int
    {
        if ($this->option('sync')) {
            SyncCatalogJob::dispatchSync();
            $this->info('Catalogue sync finished.');
        } else {
            SyncCatalogJob::dispatch();
            $this->info('Catalogue sync queued.');
        }

        return self::SUCCESS;
    }
}
