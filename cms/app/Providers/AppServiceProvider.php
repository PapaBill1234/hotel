<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        view()->composer([
            'hotel.*',
            'layouts.hotel-process',
            'layouts.hotel-community',
            'layouts.hotel-register',
            'layouts.hotel-faq',
            'layouts.hotel-iot',
            'layouts.housekeeping',
            'housekeeping.*',
        ], function ($view): void {
            $view->with('shortname', \App\Support\Hotel::shortname());
            $view->with('onlineCount', \App\Support\Hotel::onlineCount());
            $view->with('hotelOnline', (bool) config('hotel.hotel_online'));
            $view->with('faqFooter', \App\Support\Hotel::faqFooterLinks());
            if (! $view->offsetExists('hotelUser')) {
                $view->with('hotelUser', request()->attributes->get('hotelUser'));
            }
        });
    }


    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
