<?php

namespace App\Http\Middleware;

use App\Support\Hotel;
use App\Support\PolarisUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HotelMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Hotel::setting('maintenance_mode', '0') !== '1') {
            return $next($request);
        }

        if ($request->is('housekeeping', 'housekeeping/', 'up', 'health', 'health/*', 'web-gallery/*', 'housekeeping/images/*', 'housekeeping/favicon.ico', 'habbo-imaging/*', 'cacheCheck', 'clientlog', 'clientlog/*', 'mod/localizations')) {
            return $next($request);
        }

        $user = $request->attributes->get('hotelUser');
        if ($user instanceof PolarisUser && $user->isStaff()) {
            return $next($request);
        }

        $id = (int) $request->session()->get('hotel.user_id', 0);
        if ($id > 0) {
            try {
                $rank = (int) \Illuminate\Support\Facades\DB::connection('holodb')->table('users')->where('id', $id)->value('rank');
                if ($rank > 4) {
                    return $next($request);
                }
            } catch (\Throwable) {
            }
        }

        return response()->view('hotel.maintenance', [
            'pageName' => 'Maintenance',
            'bodyId' => 'maintenance',
        ], 503);
    }
}
