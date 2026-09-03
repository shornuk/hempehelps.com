<?php

use App\Catalog\Jobs\SyncCatalogJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SyncCatalogJob)->dailyAt('02:15');
