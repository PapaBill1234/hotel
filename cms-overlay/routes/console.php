<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (): void {
    Redis::connection('flags')->set('hotel:scheduler:heartbeat', now()->toIso8601String());
})->everyMinute()->name('hotel-heartbeat');

Schedule::command('horizon:snapshot')->everyFiveMinutes();
Schedule::command('queue:prune-failed --hours=48')->daily();
