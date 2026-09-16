<?php

namespace App\Providers;

use App\Support\HolodbWriteGuard;
use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Throwable;

class HolodbReadOnlyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! config('database.connections.holodb')) {
            return;
        }

        $connection = DB::connection('holodb');

        $connection->beforeExecuting(function (string $query): void {
            HolodbWriteGuard::assertReadOnly($query);
        });

        Event::listen(ConnectionEstablished::class, function (ConnectionEstablished $event): void {
            if ($event->connectionName !== 'holodb') {
                return;
            }

            try {
                $event->connection->unprepared('SET SESSION TRANSACTION READ ONLY');
            } catch (Throwable) {
                // Boot must not die if XAMPP MySQL is down. /health/ready reports it.
            }
        });
    }
}
