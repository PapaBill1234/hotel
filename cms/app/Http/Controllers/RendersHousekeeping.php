<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

trait RendersHousekeeping
{
    protected function hk(Request $request, string $pageName, string $category, string $view, array $data = []): View
    {
        return view($view, array_merge([
            'pageName' => $pageName,
            'category' => $category,
            'hotelUser' => $request->attributes->get('hotelUser'),
            'notice' => $request->session()->get('hk_notice'),
            'error' => $request->session()->get('hk_error'),
            'hkDate' => date('l F j, Y | g:iA'),
        ], $data));
    }

    protected function holodbHas(string $table): bool
    {
        try {
            return Schema::connection('holodb')->hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }

    protected function holodb()
    {
        return DB::connection('holodb');
    }

    protected function polarisRconConfigured(): bool
    {
        return filled(config('hotel.polaris_cms_url', env('POLARIS_CMS_URL')))
            || (filled(env('POLARIS_RCON_HOST')) && filled(env('POLARIS_RCON_PORT')));
    }
}
