<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\HolodbReadOnlyServiceProvider;
use App\Providers\HorizonServiceProvider;
use Laravel\Horizon\Horizon;

$providers = [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    HolodbReadOnlyServiceProvider::class,
];

if (class_exists(Horizon::class)) {
    $providers[] = HorizonServiceProvider::class;
}

return $providers;
