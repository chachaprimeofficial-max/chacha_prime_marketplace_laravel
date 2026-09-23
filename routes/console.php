<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('marketplace:status', function () {
    $this->info('Chacha Prime Marketplace application is ready.');
})->purpose('Show marketplace application status');
