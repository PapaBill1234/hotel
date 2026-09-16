<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;

class HorizonServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! class_exists(Horizon::class)) {
            return;
        }

        Horizon::auth(function (): bool {
            return app()->environment('local');
        });
    }
}
