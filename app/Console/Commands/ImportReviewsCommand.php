<?php

namespace App\Console\Commands;

use App\Reviews\Jobs\ImportReviewsJob;
use Illuminate\Console\Command;

class ImportReviewsCommand extends Command
{
    protected $signature = 'reviews:import {path : JSON or CSV path on the local disk}';

    protected $description = 'Idempotently import Judge.me reviews into Laravel';

    public function handle(): int
    {
        $unassigned = (new ImportReviewsJob($this->argument('path')))->handle();

        $this->info('Import complete.');

        if ($unassigned !== []) {
            $this->warn(count($unassigned).' reviews could not be matched to a product:');

            foreach (array_slice($unassigned, 0, 20) as $row) {
                $this->line('- '.json_encode($row));
            }
        }

        return self::SUCCESS;
    }
}
