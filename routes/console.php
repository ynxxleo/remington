<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bots:settle', function () {
    if (! \App\Models\Extension::where('id', 1)->where('installed', 1)->exists()) {
        $this->line('Bot Trader is not installed; skipping settlement.');
        return 0;
    }

    $response = app(\App\Http\Controllers\CronController::class)->botResult();
    $counts = $response->getData(true);
    $this->line('Processed: '.$counts['processed'].'; skipped: '.$counts['skipped'].'; failed: '.$counts['failed']);

    return $counts['failed'] > 0 ? 1 : 0;
})->purpose('Complete expired bot contracts and credit their wallets');
