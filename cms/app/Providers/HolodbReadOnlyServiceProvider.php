<?php

namespace App\Providers;

use App\Support\HolodbWriteGuard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class HolodbReadOnlyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! config('database.connections.holodb')) {
            return;
        }

        DB::connection('holodb')->beforeExecuting(function (string $query): void {
            HolodbWriteGuard::assertReadOnly($query);
        });
    }
}
